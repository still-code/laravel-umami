<?php

use Illuminate\Support\Carbon;

return [
    /**
     * the server URL, including the trailing `/api`
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
     * default website ID on your umami server.
     */
    'website_id' => env('UMAMI_WEBSITE_ID', null),

    /**
     * default cache key.
     */
    'cache_key' => 'umami.stats',

    /**
     * cache ttl
     * \DateTimeInterface|\DateInterval|int|null.
     */
    'cache_ttl' => Carbon::parse('1 day'),
];
