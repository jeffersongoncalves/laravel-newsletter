<div class="filament-hidden">

![Laravel Newsletter](https://raw.githubusercontent.com/jeffersongoncalves/laravel-newsletter/master/art/jeffersongoncalves-laravel-newsletter.png)

</div>

# Laravel Newsletter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-newsletter)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-newsletter/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-newsletter/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-newsletter/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-newsletter/actions?query=workflow%3A%22Fix+PHP+code+style+issues%22+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-newsletter.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-newsletter)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-newsletter.svg?style=flat-square)](LICENSE.md)

Compose, schedule and send double opt-in email newsletters with open/click tracking for Laravel applications.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-newsletter
```

The package uses Laravel's auto-discovery, so the service provider and facade are registered automatically.

### Publish Configuration

```bash
php artisan vendor:publish --tag=newsletter-config
```

### Publish Migrations

```bash
php artisan vendor:publish --tag=newsletter-migrations
```

### Run Migrations

```bash
php artisan migrate
```

### Publish Translations (optional)

```bash
php artisan vendor:publish --tag=newsletter-translations
```

## Configuration

The configuration file is located at `config/newsletter.php`:

```php
return [
    // URI prefix for subscribe/confirm/unsubscribe/tracking/webview routes
    'route_prefix' => 'newsletter',

    // Email group used when a subscription request does not specify one
    'default_email_group' => 'Website',

    // Registers `newsletter:send-scheduled` on the schedule (hourly)
    'schedule_enabled' => true,

    // Tag outgoing links with UTM parameters and inject the open-tracking pixel
    'tracking_enabled' => true,
];
```

## Usage

### Composing a Newsletter

```php
use JeffersonGoncalves\Newsletter\Models\Newsletter;
use JeffersonGoncalves\Newsletter\Enums\NewsletterContentType;

$newsletter = Newsletter::create([
    'subject' => 'Monthly Update',
    'sender_name' => 'Example Co',
    'sender_email' => 'news@example.com',
    'content' => '<p>Hello there!</p>',
    'content_type' => NewsletterContentType::Html,
    'send_unsubscribe_link' => true,
    'send_webview_link' => true,
    'route' => 'monthly-update',
    'published' => true,
    'utm_campaign' => 'monthly-update',
]);

$emailGroup = \JeffersonGoncalves\Newsletter\Models\EmailGroup::firstOrCreate(['title' => 'Website']);

$newsletter->emailGroups()->attach($emailGroup);
```

Attachments can be added through the `attachments` media collection (powered by `spatie/laravel-medialibrary`):

```php
$newsletter->addMedia($uploadedFile)->toMediaCollection('attachments');
```

### Subscribing (Double Opt-in)

```php
use JeffersonGoncalves\Newsletter\Facades\Newsletter;

$member = Newsletter::subscribe('subscriber@example.com', 'Website');
// $member->confirmed_at is null until the confirmation link is clicked
```

A confirmation email with a signed link is queued automatically. Once the subscriber clicks it (`GET /newsletter/confirm/{emailGroupMember}`), `confirmed_at` is set. Unsubscribing works the same way via `GET /newsletter/unsubscribe/{emailGroupMember}`.

### Sending

```php
use JeffersonGoncalves\Newsletter\Facades\Newsletter;

Newsletter::send($newsletter);
```

This resolves every confirmed, non-unsubscribed member of the newsletter's email groups (excluding anyone it was already sent to), dispatches one queued job per recipient inside a batch, and marks the newsletter as `sent` once the batch completes. Delivery outcomes are recorded per recipient in `NewsletterSentRecipient`.

### Sending a Test Copy

```php
Newsletter::sendTest($newsletter, 'preview@example.com');
```

Renders and sends synchronously to a single address without touching delivery tracking.

### Scheduling

```php
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;

$newsletter->update([
    'status' => NewsletterStatus::Scheduled,
    'scheduled_at' => now()->addDay(),
]);
```

The `newsletter:send-scheduled` command runs hourly by default (disable via `NEWSLETTER_SCHEDULE_ENABLED=false`) and sends every newsletter whose `scheduled_at` is due.

### Tracking

When `tracking_enabled` is on, every external link in the rendered email is tagged with `utm_source`, `utm_medium` and `utm_campaign`. When a newsletter has `send_webview_link` enabled, a 1x1 open-tracking pixel is appended, incrementing `total_views` on each open.

### Detecting Broken Links

```php
use JeffersonGoncalves\Newsletter\Actions\FindBrokenNewsletterLinksAction;

$broken = app(FindBrokenNewsletterLinksAction::class)->handle($newsletter);
```

Returns a collection of `['url' => ..., 'status' => int|null, 'error' => string|null]` for every link/image that returned an error status or could not be reached.

## Events

`NewsletterSent`, `NewsletterScheduled`, `EmailGroupMemberSubscribed`, `EmailGroupMemberConfirmed`, `EmailGroupMemberUnsubscribed`.

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

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
