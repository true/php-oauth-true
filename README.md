# TrueAccounts Provider for OAuth 2.0 Client

[![Build Status](https://img.shields.io/travis/true/php-oauth-true.svg)](https://travis-ci.org/true/php-oauth-true)
[![License](https://img.shields.io/packagist/l/true/oauth-true-accounts.svg)](https://github.com/thephptrue/oauth-true-accounts/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/true/oauth-true-accounts.svg)](https://packagist.org/packages/true/oauth-true-accounts)

This package provides TrueAccounts OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/true/oauth-true-accounts).

## Requirements

The following versions of PHP are supported.

* PHP 7.0
* PHP 7.1
* PHP 7.2

## Installation

To install, use composer:

```
composer require true/oauth-true-accounts
```

## Usage

### Client Credentials Flow

```php
$provider = new True\OAuth2\Provider\TrueAccounts([
    'clientId'     => '<client id>',
    'clientSecret' => '<client secret>',
]);

$token = $provider->getAccessToken('client_credentials', [
    'scope' => 'profile dns',
]);

```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](https://github.com/true/oauth-true-accounts/blob/master/LICENSE) for more information.
