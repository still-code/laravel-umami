<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait Websites
{
    /**
     * get all available websites.
     *
     * @param  $force  boolean force getting the result from the server, and clear the cache
     *
     * @throws RequestException
     * @throws \Exception
     */
    public static function websites(bool $force = false, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->get(config('umami.url').'/websites');

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.websites');
        }

        return cache()->remember(config('umami.cache_key').'.websites', config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    /**
     * @return array|mixed
     *
     * @throws RequestException
     */
    public static function createWebsite(array $data, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->post(config('umami.url').'/websites', $data);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function updateWebsite(string $websiteUuid, array $data, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->post(config('umami.url').'/websites/'.$websiteUuid, $data);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function deleteWebsite($websiteUuid, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->delete(config('umami.url').'/websites/'.$websiteUuid);

        $response->throw();

        return $response->json();
    }
}
