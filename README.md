# This package brings discord support to seatplus

[![Latest Version on Packagist](https://img.shields.io/packagist/v/seatplus/discord.svg?style=flat-square)](https://packagist.org/packages/seatplus/discord)
[![GitHub Tests Action Status](https://github.com/seatplus/discord/actions/workflows/run-tests.yml/badge.svg)](https://github.com/seatplus/discord/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://github.com/seatplus/discord/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/seatplus/discord/actions/workflows/fix-php-code-style-issues.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/45929a24a96863ff9f46/maintainability)](https://codeclimate.com/github/seatplus/discord/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/45929a24a96863ff9f46/test_coverage)](https://codeclimate.com/github/seatplus/discord/test_coverage)
[![Total Downloads](https://img.shields.io/packagist/dt/seatplus/discord.svg?style=flat-square)](https://packagist.org/packages/seatplus/discord)

---
This repo brings discord tribes to seatplus.

## Installation

You can install the package via composer:

```bash
composer require seatplus/discord
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="web"
php artisan migrate
```

## Setup

to setup the package you need to create a discord application and bot. Finally you must add the credentials to your .env file

### Create a discord application

* Go to https://discord.com/developers/applications
* Create a new application and give it a name
* Go to OAuth2
  * Add a redirect url (e.g. {seatplus-public-url}/discord/callback)
* Go to Bot
  * Add a bot
  * Enable "require OAuth2 code grant"
  * Enable "Server Members Intent"

### Retrieve credentials

Below you find instructions on where to find the credentials and how to fill them into your .env file

* From the oauth2 tab copy the `client id`, `client secret` and `redirect uri` tab copy the `token` (sometimes you need to reset the token first)

```dotenv
DISCORD_CLIENT_ID=  // your discord client id
DISCORD_CLIENT_SECRET= // your discord client secret
DISCORD_BOT_TOKEN= // your discord bot token
DISCORD_REDIRECT_URI= // your discord redirect uri
```


## Usage

```bash
php artisan tribe:nickname:discord
php artisan tribe:role:discord
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Felix Huber](https://github.com/seatplus)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
