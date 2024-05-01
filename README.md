# Statamic Postmark Spamcheck

This addon checks any form submission content against the [Postmark Spamcheck API](https://spamcheck.postmarkapp.com).

## Installation

Install by composer: `composer require thoughtco/statamic-postmark-spamcheck`

## Configuration

By default all Form Submissions will be checked for the presence of a `text` or `textarea` field, and if found a check will be run.

If you want to override this, publish the config:

`php artisan vendor:publish --tag=statamic-postmark-spamcheck`

You then have the option to specify an array of specific forms to check, what your spam threshold is, what field handle to check for and whether you want to fail silently.

A basic text email body is passed to the Postmark API, if you want to override this you can publish the view and modify it:

`php artisan vendor:publish --tag=statamic-postmark-spamcheck-views`

The publishe file will be found in `resources/views/vendor/statamic-postmark-spamcheck`


## Testing during development

If you want to test responses during development you can use the `STATAMIC_POSTMARK_SPAMCHECK_TEST_MODE` env value.

Setting it to `disable` will prevent the addon from running.

Setting it to `fail` with throw a validation error, or fail silently, depending on what the `fail_silently` config value is set to.
