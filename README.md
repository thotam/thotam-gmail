# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thotam/thotam-gmail.svg?style=flat-square)](https://packagist.org/packages/thotam/thotam-gmail)
[![Build Status](https://img.shields.io/travis/thotam/thotam-gmail/master.svg?style=flat-square)](https://travis-ci.org/thotam/thotam-gmail)
[![Quality Score](https://img.shields.io/scrutinizer/g/thotam/thotam-gmail.svg?style=flat-square)](https://scrutinizer-ci.com/g/thotam/thotam-gmail)
[![Total Downloads](https://img.shields.io/packagist/dt/thotam/thotam-gmail.svg?style=flat-square)](https://packagist.org/packages/thotam/thotam-gmail)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require thotam/thotam-gmail
```

## Usage

### Add this to .env

```php
DEFAULT_GOOGLE_MAIL_CLIENT_ID=""
DEFAULT_GOOGLE_MAIL_CLIENT_SECRET=""
DEFAULT_GOOGLE_MAIL_REFRESH_TOKEN=""
SurveyMail_GOOGLE_MAIL_REFRESH_TOKEN=""
BuddyMail_GOOGLE_MAIL_REFRESH_TOKEN=""
KbytMail_GOOGLE_MAIL_REFRESH_TOKEN=""
```

### Add HasMailTraits to you Model you want to you

```php
use Thotam\ThotamGmail\Traits\HasMailTraits;
```

### How to send and reply

```php
use Thotam\ThotamGmail\Services\Message\Mail
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email thanhtamtqno1@gmail.com instead of using the issue tracker.

## Credits

-   [thotam](https://github.com/thotam)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
