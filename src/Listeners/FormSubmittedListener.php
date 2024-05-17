<?php

namespace Thoughtco\StatamicPostmarkSpamcheck\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Statamic\Events\FormSubmitted;

class FormSubmittedListener
{
    public function handle(FormSubmitted $event)
    {
        $submission = $event->submission->data();
        $form = $event->submission->form();
        $handle = $form->handle();

        $forms = config('statamic-postmark-spamcheck.forms', 'all');

        if ($forms !== 'all') {
            if (! in_array($handle, $forms)) {
                return;
            }
        }

        if (app()->environment() != 'production') {
            $testMode = config('statamic-postmark-spamcheck.test_mode', 'off');

            if ($testMode == 'disable') {
                return;
            }

            if ($testMode == 'fail') {
                if (config('statamic-postmark-spamcheck.fail_silently')) {
                    return false;
                }

                $this->throwFailure();
            }
        }

        $content = $form->blueprint()
            ->fields()->all()
            ->filter(fn ($field) => $field->type() == 'textarea' || ($field->type() == 'text' && $field->get('input_type', '') != 'email'))
            ->map(function ($field) use ($submission) {
                return $submission->get($field->handle());
            })
            ->filter()
            ->join("\n");

        if (! $content) {
            return;
        }

        $email = $form->blueprint()
            ->fields()->all()
            ->filter(fn ($field) => $field->type() == 'text' && $field->get('input_type', '') != 'email')
            ->map(function ($field) use ($submission) {
                return $submission->get($field->handle());
            })
            ->filter()
            ->first() ?? '';

        $body = view('statamic-postmark-spamcheck::email', [
            'content' => $content,
            'date' => now(),
            'email' => $email,
        ])->render();

        $response = Http::withBody($content, 'text/plain')
            ->post('https://spamcheck.postmarkapp.com/filter', [
                'email' => $body,
                'options' => 'short',
            ]);

        $json = $response->json();

        // handle postmark error
        if (! ($json['success'] ?? false)) {
            return;
        }

        if ($score = Arr::get($json, 'score', false)) {

            if ($score >= config('statamic-postmark-spamcheck.threshold')) {
                if (config('statamic-postmark-spamcheck.fail_silently')) {
                    return false;
                }

                $this->throwFailure();
            }
        }
    }

    public function throwFailure()
    {
        throw ValidationException::withMessages([
            '_unspecified' => __('Failed spam check'),
        ]);
    }
}
