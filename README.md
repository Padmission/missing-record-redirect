# This is my package missing-record-redirect

[![Latest Version on Packagist](https://img.shields.io/packagist/v/padmission/missing-record-redirect.svg?style=flat-square)](https://packagist.org/packages/padmission/missing-record-redirect)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/padmission/missing-record-redirect/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/padmission/missing-record-redirect/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/padmission/missing-record-redirect/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/padmission/missing-record-redirect/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/padmission/missing-record-redirect.svg?style=flat-square)](https://packagist.org/packages/padmission/missing-record-redirect)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require padmission/missing-record-redirect
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="missing-record-redirect-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="missing-record-redirect-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="missing-record-redirect-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$missingRecordRedirect = new Padmission\MissingRecordRedirect();
echo $missingRecordRedirect->echoPhrase('Hello, Padmission!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Padmission](https://github.com/Padmission)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
