<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait Accounts
{
    /**
     * @throws RequestException
     */
    public static function accounts(bool $force = false): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->get(config('umami.url').'/accounts');

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.accounts');
        }

        return cache()->remember(config('umami.cache_key').'.accounts', config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    /**
     * @throws RequestException
     */
    public static function createAccount(string $username, string $password): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->post(config('umami.url').'/accounts', [
                'password' => $password,
                'username' => $username,
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function updateAccount(string $userId, array $data): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->post(config('umami.url').'/accounts/'.$userId, $data);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function deleteAccount($userId): mixed
    {
        $response = Http::withToken(session('umami_token'))
            ->delete(config('umami.url').'/accounts/'.$userId);

        $response->throw();

        return $response->json();
    }
}
