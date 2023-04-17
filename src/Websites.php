<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait Websites
{
    /**
     * get all available websites.
     *
     * @param $force boolean force getting the result from the server, and clear the cache
     *
     * @throws RequestException
     * @throws \Exception
     */
    public static function websites(bool $force = false): mixed
    {
        self::auth();

        $response = Http::withToken(session('umami_token'))
            ->get(config('umami.url') . '/websites');

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key') . '.websites');
        }

        return cache()->remember(config('umami.cache_key') . '.websites', config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }
}
