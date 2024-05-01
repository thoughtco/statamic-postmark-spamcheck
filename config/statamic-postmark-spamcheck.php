<?php

return [
    'forms' => 'all', // or add an array of form handles eg ['form1', 'form2']

    'fail_silently' => true,

    'test_mode' => env('STATAMIC_POSTMARK_SPAMCHECK_TEST_MODE', 'off'),

    'threshold' => 5,
];
