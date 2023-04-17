<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Umami
{
    use Websites;

    /**
     * authenticate the user with umami stats' server.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|void
     *
     * @throws RequestException
     */
    public function __construct()
    {
        abort_if(
            config('umami.url') === null ||
            config('umami.username') === null ||
            config('umami.password') === null, 421, 'please make sur to set all umami config');

        $response = Http::post(config('umami.url').'/auth/login', [
            'username' => config('umami.username'),
            'password' => config('umami.password'),
        ]);

        $response->throw();

        session()->put('umami_token', $response->json()['token']);
    }

    /**
     * @param $siteID string require site id
     * @param $part string available parts: stats, pageviews, events, metrics. defualt:
     * @param $options array|null available options: start_at, end_at, unit, tz, type
     * @param $force bool force getting the result from the server, and clear the cache
     *
     * @throws RequestException
     * @throws \Exception
     */
    public static function query(string $siteID, string $part = 'stats', array $options = null, bool $force = false): mixed
    {
        $options = self::setOptions($part, $options);
        $response = Http::withToken(session('umami_token'))
            ->get(config('umami.url').'/websites/'.$siteID.'/'.$part, $options);

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.'.$siteID.'.'.$part);
        }

        return cache()->remember(config('umami.cache_key').'.'.$siteID.'.'.$part, config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    /**
     * set the defaults options for the $part.
     */
    private static function setOptions($part, $options): array
    {
        $defaultOptions = [
            'websites' => [],
            'stats' => [],
            'pageviews' => [
                'unit' => 'day',
                'tz' => config('app.timezone'),
            ],
            'events' => [
                'unit' => 'day',
                'tz' => config('app.timezone'),
            ],
            'metrics' => [
                'type' => 'url',
            ],
        ];

        $datesOptions = [
            'start_at' => now()->subDays(7)->getTimestampMs(),
            'end_at' => now()->getTimestampMs(),
        ];

        if ($options === null) {
            return array_merge($defaultOptions[$part], $datesOptions);
        }

        $datesOptions = [
            'start_at' => formatDate($options['start_at']),
            'end_at' => formatDate($options['end_at']),
        ];

        return array_merge($defaultOptions[$part], array_merge($options, $datesOptions));
    }
}
