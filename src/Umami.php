<?php

namespace Umami;

use Illuminate\Support\Facades\Http;

class Umami
{
    /**
     * authenticate the user with stats server.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function auth()
    {
        abort_if(
            config('umami.url') === null ||
            config('umami.username') === null ||
            config('umami.password') === null, 421, 'please make sur to set all umami config');

        if (session()->has('umami_token')) {
            return session('umami_token');
        }

        $response = Http::post(config('umami.url').'/auth/login', [
            'username' => config('umami.username'),
            'password' => config('umami.password'),
        ]);

        $response->throw();

        session()->put('umami_token', $response->json()['token']);
    }

    /**
     * @param $siteID int require site id
     * @param $part string available parts: stats, pageviews, events, metrics. defualt:
     * @param $options array available options: start_at, end_at, unit, tz, type
     * @return array|mixed
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function stats($siteID, $part = 'stats', $options = null)
    {
        self::auth();

        $options = self::setOptions($part, $options);

        $response = Http
            ::withHeaders([
                'Cookie' => 'umami.auth='.session('umami_token'),
            ])
            ->get(config('umami.url').'/website/'.$siteID.'/'.$part, $options);

        $response->throw();

        return $response->json();
    }

    /**
     * get all available websites
     * @return array|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function websites()
    {
        self::auth();

        $response = Http
            ::withHeaders([
                'Cookie' => 'umami.auth='.session('umami_token'),
            ])
            ->get(config('umami.url').'/websites');

        $response->throw();

        return $response->json();
    }

    /**
     * set the defaults options for the $part.
     *
     * @param $part
     * @param $options
     * @return array
     */
    public static function setOptions($part, $options)
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
            'start_at' => self::setDate($options['start_at']),
            'end_at' => self::setDate($options['end_at']),
        ];

        return array_merge($defaultOptions[$part], array_merge($options, $datesOptions));
    }

    /**
     * set the Carbon dates and convert them to milliseconds.
     *
     * @param $data
     * @return int|null
     */
    public static function setDate($data)
    {
        if (is_numeric($data)) {
            return $data;
        }

        if (is_object($data)) {
            return $data->getTimestampMs();
        }

        return null;
    }
}
