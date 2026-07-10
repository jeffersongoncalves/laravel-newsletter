## Laravel Newsletter Package

The `jeffersongoncalves/laravel-newsletter` package composes, schedules and sends double opt-in email newsletters with open/click tracking for Laravel applications.

### Package Namespace

All classes are under `JeffersonGoncalves\Newsletter`.

### Installation

@verbatim
<code-snippet name="Install the package" lang="bash">
composer require jeffersongoncalves/laravel-newsletter
php artisan vendor:publish --tag=newsletter-config
php artisan vendor:publish --tag=newsletter-migrations
php artisan migrate
</code-snippet>
@endverbatim

### Architecture

- **Facade**: `JeffersonGoncalves\Newsletter\Facades\Newsletter` - primary entry point (`send`, `sendTest`, `subscribe`, `confirm`, `unsubscribe`)
- **Models**: `Newsletter`, `EmailGroup`, `EmailGroupMember`, `NewsletterSentRecipient`
- **Actions**: one class per operation under `src/Actions`, each with a single `handle()` method
- **Enums**: `NewsletterStatus`, `NewsletterContentType`, `RecipientDeliveryStatus`
- **Jobs**: `SendNewsletterEmailJob` (per recipient, part of a `Bus::batch()`), `SendScheduledNewslettersJob`

### Features

- **Double opt-in subscriptions**: `Newsletter::subscribe($email, $groupTitle)` creates an unconfirmed `EmailGroupMember` and queues a confirmation email with a signed link.
- **Sending**: `Newsletter::send($newsletter)` resolves confirmed, non-unsubscribed recipients and dispatches one queued job per recipient inside a batch. When the batch finishes, the newsletter status becomes `sent`.

@verbatim
<code-snippet name="Sending a newsletter" lang="php">
use JeffersonGoncalves\Newsletter\Facades\Newsletter;
use JeffersonGoncalves\Newsletter\Models\Newsletter as NewsletterModel;

$newsletter = NewsletterModel::find($id);

Newsletter::send($newsletter);
</code-snippet>
@endverbatim

- **Tracking**: outgoing links are tagged with UTM parameters, and an open-tracking pixel is injected when `send_webview_link` is enabled on the newsletter.
- **Scheduling**: set `status` to `scheduled` and `scheduled_at` on a newsletter; the `newsletter:send-scheduled` command (scheduled hourly by default) picks it up once due.
- **Broken link detection**: `FindBrokenNewsletterLinksAction` checks every link/image URL in a newsletter's content and reports unreachable or erroring ones.

### Configuration

@verbatim
<code-snippet name="Config example" lang="php">
// config/newsletter.php
return [
    'route_prefix' => env('NEWSLETTER_ROUTE_PREFIX', 'newsletter'),
    'default_email_group' => env('NEWSLETTER_DEFAULT_EMAIL_GROUP', 'Website'),
    'schedule_enabled' => env('NEWSLETTER_SCHEDULE_ENABLED', true),
    'tracking_enabled' => env('NEWSLETTER_TRACKING_ENABLED', true),
];
</code-snippet>
@endverbatim

### Best Practices

- Always send newsletters through the `SendNewsletterAction` (or the `Newsletter` facade) instead of manually dispatching `SendNewsletterEmailJob` — it validates the newsletter, resolves recipients, and manages the status transitions.
- Use `EmailGroup`/`EmailGroupMember` to manage mailing lists; never send directly to arbitrary addresses outside of `sendTest()`.
- Attachments for a newsletter are managed through the `attachments` media collection (spatie/laravel-medialibrary) on the `Newsletter` model.
