<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URI prefix under which the package's subscribe, confirm, unsubscribe,
    | tracking and public webview routes are registered.
    |
    */

    'route_prefix' => env('NEWSLETTER_ROUTE_PREFIX', 'newsletter'),

    /*
    |--------------------------------------------------------------------------
    | Default Email Group
    |--------------------------------------------------------------------------
    |
    | The email group title used when a subscription request does not specify
    | which group the subscriber should join.
    |
    */

    'default_email_group' => env('NEWSLETTER_DEFAULT_EMAIL_GROUP', 'Website'),

    /*
    |--------------------------------------------------------------------------
    | Scheduled Sending
    |--------------------------------------------------------------------------
    |
    | When enabled, the package registers the `newsletter:send-scheduled`
    | command on the application's schedule to run hourly.
    |
    */

    'schedule_enabled' => env('NEWSLETTER_SCHEDULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    |
    | When enabled, outgoing newsletter links are tagged with UTM parameters
    | and, if requested per newsletter, a webview open-tracking pixel is
    | injected into the rendered email.
    |
    */

    'tracking_enabled' => env('NEWSLETTER_TRACKING_ENABLED', true),

];
