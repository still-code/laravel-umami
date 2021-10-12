<?php

use Illuminate\Support\Carbon;

return [
    /**
     * the server URL with the /api.
     */
    'url' => env('UMAMI_URL', null),

    /**
     * the username for your umami server.
     */
    'username' => env('UMAMI_USERNAME', null),

    /**
     * the password for your umami server.
     */
    'password' => env('UMAMI_PASSWORD', null),

    /**
     * default cache key.
     */
    'cache_key' => 'umami.stats',

    /**
     * cache key
     * \DateTimeInterface|\DateInterval|int|null.
     */
    'cache_ttl' => Carbon::parse('1 day'),
];
