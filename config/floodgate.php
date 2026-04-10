<?php

use TestMonitor\Floodgate\Notifications\SummaryNotification;

return [

    /*
    |--------------------------------------------------------------------------
    | Buffer Delay
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait before flushing a notification buffer.
    | During this window, additional notifications of the same type are
    | grouped and sent as a single summary.
    |
    */

    'delay' => 10,

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | The cache store used to hold throttled notifications. Defaults to your
    | application's default cache store when set to null. Must support
    | atomic locks (e.g. Redis, Memcached, database).
    |
    */

    'cache' => [
        'store' => null,
        'prefix' => 'floodgate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Summary Notification
    |--------------------------------------------------------------------------
    |
    | Configuration for the summary notification sent when multiple
    | notifications are buffered within the delay window.
    |
    */

    'summary' => SummaryNotification::class,

];
