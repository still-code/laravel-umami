<p align="center"><img src="https://banners.beyondco.de/Laravel%20Umami.png?theme=light&packageManager=composer+require&packageName=atm-code%2Flaravel-umami&pattern=temple&style=style_1&description=Umami+API+wrapper+for+Laravel&md=0&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" width="600"></p>

<p align="center">
<a href="https://packagist.org/packages/atm-code/laravel-umami"><img src="https://img.shields.io/packagist/v/atm-code/laravel-umami" /></a>
<a href="https://travis-ci.com/atm-code/laravel-umami"><img src="https://img.shields.io/travis/com/atm-code/laravel-umami" /></a>
<a href="https://github.styleci.io/repos/354853609?branch=main"><img src="https://github.styleci.io/repos/354853609/shield?branch=main" /></a>
<a href="https://packagist.org/packages/atm-code/laravel-umami"><img src="https://img.shields.io/packagist/dt/atm-code/laravel-umami" /></a>
<a href="https://github.com/atm-code/laravel-umami"><img src="https://img.shields.io/github/stars/atm-code/laravel-umami" /></a>
</p>

# Umami API wrapper for laravel

API wrapper for umami website analytics. get your stats in the laravel app.

check out [Umami](https://umami.is/) own your website analytics

## Installation

You can install the package via composer:

```bash
composer require atm-code/laravel-umami
```
### Config
to publish the configuration file:

```bash
php artisan vendor:publish --provider="Umami\UmamiServiceProvider"
```

and then add these to your `.env` file:

```bash
UMAMI_URL=https://stats-site-com/api
UMAMI_USERNAME=username
UMAMI_PASSWORD="password"
```

## Usage

```php
$stats = \Umami\Umami::stats(2,'metrics',[
        'start_at'=>today()->subDays(7),
        'end_at'=>today(),
        'type'=>'referrer',
    ]);
```

## Parameters

### Site id

required: site id from umami server

```php
$stats = \Umami\Umami::stats(siteID);
```

### Part

required: the stats part you want to get from umami,

available options : `stats, pageviews, events, metrics`

default: `stats`

```php
$stats = \Umami\Umami::stats(siteID,'pageviews');
```

## Options

### Dates (start_at,end_at)

optional: Timestamp of starting and end date,

default: last 7 days

you can pass `carbon` object or timestamp in milliseconds

```php
$stats = \Umami\Umami::stats(siteID,'metrics',[
    'start_at'=>today()->subDays(7),
    'end_at'=>today(),
]);
```

### unit
only available on `pageviews` and `events`

optional: Time unit, available options: `year, month, hour, day`,

default: day

```php
$stats = \Umami\Umami::stats(siteID,'metrics',[
    'unit'=>'year',
]);
```

### Timezone (tz)
optional: Timezone,

only available on `pageviews` and `events`

default: config('app.timezone')

```php
$stats = \Umami\Umami::stats(siteID,'metrics',[
    'tz'=>'America/Los_Angeles',
]);
```

### type (for metrics only)

optional: Gets metrics for a given time range,

available options: `url, referrer, browser, os, device, country, event`,

default: url

```php
$stats = \Umami\Umami::stats(siteID,'metrics',[
    'tz'=>'America/Los_Angeles',
]);
```

## More details

Please checkout [Umami website](https://umami.is/) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Ashraf Monshi](https://github.com/atm-code)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
