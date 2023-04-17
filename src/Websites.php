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
        $response = Http::withToken(session('umami_token'))
            ->get(config('umami.url').'/websites');

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
    public static function createWebsite(string $domain, string $name, bool $share = false, bool $public = false): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->post(config('umami.url').'/websites', [
                'domain' => $domain,
                'name' => $name,
                'share' => $share,
                'public' => $public,
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function updateWebsite(string $websiteUuid, array $data): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->post(config('umami.url').'/websites/'.$websiteUuid, $data);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function deleteWebsite($websiteUuid): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->delete(config('umami.url').'/websites/'.$websiteUuid);

        $response->throw();

        return $response->json();
    }
}
