<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Umami
{
    use Websites;
    use Users;

    /**
     * authenticate the user with umami stats' server.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|void
     *
     * @throws RequestException
     */
    public static function auth()
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
     * @param $part string available parts: stats, active, pageviews, events, metrics. default:stats
     * @param $options array|null available options: startAt, endAt, unit, tz, type
     * @param $force bool force getting the result from the server, and clear the cache
     *
     * @throws RequestException
     * @throws \Exception
     */
    public static function query(string $siteID, string $part = 'stats', array $options = null, bool $force = false): mixed
    {
        //http://localhost:3000/api/websites/a103b4d2-1308-4052-bc5d-604b27f06b87/pageviews?unit=hour&timezone=Asia%2FRiyadh
        self::auth();

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
            'stats' => [
                'unit' => 'day',
                'timezone' => config('app.timezone'),
                'url' => null,
                'referrer' => null,
                'pageTitle' => null,
                'os' => null,
                'browser' => null,
                'device' => null,
                'country' => null,
                'region' => null,
                'city' => null,
            ],
            'pageviews' => [
                'unit' => 'day',
                'timezone' => config('app.timezone'),
                'url' => null,
                'referrer' => null,
                'pageTitle' => null,
                'os' => null,
                'browser' => null,
                'device' => null,
                'country' => null,
                'region' => null,
                'city' => null,
            ],
            'events' => [
                'unit' => 'day',
                'timezone' => config('app.timezone'),
                'url' => null,
                'eventName' => null,
            ],
            'metrics' => [
                'type' => 'url',
                'unit' => 'day',
                'timezone' => config('app.timezone'),
                'url' => null,
                'referrer' => null,
                'pageTitle' => null,
                'os' => null,
                'browser' => null,
                'device' => null,
                'country' => null,
                'region' => null,
                'city' => null,
            ],
            'active' => [],
        ];

        $datesOptions = [
            'startAt' => now()->subDays(7)->getTimestampMs(),
            'endAt' => now()->getTimestampMs(),
        ];

        if ($options === null) {
            return array_merge($defaultOptions[$part], $datesOptions);
        }

        $datesOptions = [
            'startAt' => formatDate($options['startAt']),
            'endAt' => formatDate($options['endAt']),
        ];

        return array_merge($defaultOptions[$part], array_merge($options, $datesOptions));
    }
}
