<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait Users
{
    /**
     * @throws RequestException
     */
    public static function users(bool $force = false, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->get(config('umami.url').'/users');

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.users');
        }

        return cache()->remember(config('umami.cache_key').'.users', config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    /**
     * @throws RequestException
     */
    public static function createUser(string $username, string $password, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))
            ->post(config('umami.url').'/users', [
                'password' => $password,
                'username' => $username,
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function updateUser(string $userId, array $data, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->post(config('umami.url').'/users/'.$userId, $data);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws RequestException
     */
    public static function deleteUser($userId, $authData = null): mixed
    {
        $response = Http::withToken(self::auth($authData))->delete(config('umami.url').'/users/'.$userId);

        $response->throw();

        return $response->json();
    }
}
