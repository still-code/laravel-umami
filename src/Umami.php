<?php

namespace Umami;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Umami
{
    use Users;
    use Websites;

    /**
     * authenticate the user with umami stats' server.
     *
     * @param  array|null  $authData  username and password
     */
    public static function auth(?array $authData = null): ?string
    {
        abort_if(
            config('umami.url') === null ||
            config('umami.username') === null ||
            config('umami.password') === null, 421, 'Please make sure to set all the required Umami configuration values.');

        if ($authData === null) {
            $authData = [
                'username' => config('umami.username'),
                'password' => config('umami.password'),
            ];
        }

        $response = Http::post(config('umami.url').'/auth/login', $authData);

        if ($response->ok()) {
            return $response->json()['token'];
        }

        return null;
    }

    /**
     * @param  $siteID  string require site id
     * @param  $part  string available parts: stats, active, pageviews, events, metrics. default:stats
     * @param  $options  array|null available options: startAt, endAt, unit, tz, type
     * @param  $force  bool force getting the result from the server, and clear the cache
     *
     * @throws RequestException
     * @throws \Exception
     */
    public static function query(string $siteID, string $part = 'stats', ?array $options = null, bool $force = false, $authData = null): mixed
    {
        $options = self::setOptions($part, $options);
        $response = Http::withToken(self::auth($authData))
            ->get(config('umami.url').'/websites/'.$siteID.'/'.$part, $options);

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.'.$siteID.'.'.$part);
        }

        return cache()->remember(config('umami.cache_key').'.'.$siteID.'.'.$part, config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    public static function events(string $siteID, ?array $options = null, bool $force = false, $authData = null): mixed
    {
        $part = 'event-data-events';

        $options = self::setOptions($part, $options);
        $options['websiteId'] = $siteID;

        $response = Http::withToken(self::auth($authData))
            ->get(config('umami.url').'/event-data/events', $options);

        $response->throw();

        if ($force) {
            cache()->forget(config('umami.cache_key').'.'.$siteID.'.'.$part);
        }

        return cache()->remember(config('umami.cache_key').'.'.$siteID.'.'.$part, config('umami.cache_ttl'), function () use ($response) {
            return $response->json();
        });
    }

    public static function event_fields(string $siteID, ?array $options = null, bool $force = false, $authData = null): mixed
    {
        $part = 'event-data-fields';

        $options = self::setOptions($part, $options);
        $options['websiteId'] = $siteID;

        $response = Http::withToken(self::auth($authData))
            ->get(config('umami.url').'/event-data/fields', $options);

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
            'event-data-events' => [
                'event' => null,
            ],
            'event-data-fields' => [
                //
            ],
        ];

        $datesOptions = [
            'startAt' => now()->subDays(7)->getTimestampMs(),
            'endAt' => now()->getTimestampMs(),
        ];

        if ($options === null) {
            return array_merge($defaultOptions[$part], $datesOptions);
        }

        $datesOptions = [
            'startAt' => formatDate($options['start_at']),
            'endAt' => formatDate($options['end_at']),
        ];

        return array_merge($defaultOptions[$part], array_merge($options, $datesOptions));
    }
}
