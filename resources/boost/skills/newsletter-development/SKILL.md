---
name: newsletter-development
description: Build and work with Laravel Newsletter features, including composing, scheduling, sending and tracking double opt-in email newsletters.
---

# Newsletter Development

## When to use this skill

Use this skill when:
- Composing or sending newsletters through `jeffersongoncalves/laravel-newsletter`
- Managing email groups and double opt-in subscribers
- Working with newsletter scheduling, tracking or delivery status

## Core Concepts

### Newsletter

A `Newsletter` holds the subject, sender, content (`content_type`: `rich_text`, `markdown` or `html`), status (`draft`, `scheduled`, `sending`, `sent`), and tracking counters (`total_recipients`, `total_views`). It belongs to many `EmailGroup` models through the `newsletter_email_group` pivot table.

```php
use JeffersonGoncalves\Newsletter\Models\Newsletter;
use JeffersonGoncalves\Newsletter\Enums\NewsletterContentType;

$newsletter = Newsletter::create([
    'subject' => 'Monthly Update',
    'sender_email' => 'news@example.com',
    'sender_name' => 'Example Co',
    'content' => '<p>Hello!</p>',
    'content_type' => NewsletterContentType::Html,
    'utm_campaign' => 'monthly-update',
]);

$newsletter->emailGroups()->attach($group);
```

### Email Groups & Members

`EmailGroup` represents a mailing list (e.g. "Website", "Customers"). `EmailGroupMember` rows track a single email address's subscription state per group: `unsubscribed`, `unsubscribed_at`, and `confirmed_at` (null until the double opt-in link is clicked).

```php
use JeffersonGoncalves\Newsletter\Facades\Newsletter;

$member = Newsletter::subscribe('subscriber@example.com', 'Website');
// $member->confirmed_at is null until the subscriber clicks the confirmation link
```

### Sending a Newsletter

`SendNewsletterAction` validates the newsletter can be sent, resolves pending recipients (confirmed, non-unsubscribed, not already sent-to), and dispatches one `SendNewsletterEmailJob` per recipient inside a `Bus::batch()`. When the batch finishes, the newsletter is marked `sent`.

```php
use JeffersonGoncalves\Newsletter\Facades\Newsletter;

Newsletter::send($newsletter);
```

Each recipient's outcome is recorded in `NewsletterSentRecipient` (`status`: `sent` or `failed`, with `error_message` on failure).

### Sending a Test

`sendTest()` renders and sends the newsletter synchronously to a single address, bypassing status transitions and recipient tracking — useful for previewing before a real send.

```php
Newsletter::sendTest($newsletter, 'preview@example.com');
```

## Common Patterns

### Scheduling a Send

Set `status` to `scheduled` and `scheduled_at` to a future date. The `newsletter:send-scheduled` command (registered on the schedule hourly, gated by `config('newsletter.schedule_enabled')`) dispatches `SendScheduledNewslettersJob`, which sends every due newsletter via `SendNewsletterAction`.

```php
use JeffersonGoncalves\Newsletter\Actions\ScheduleNewsletterAction;

app(ScheduleNewsletterAction::class)->handle($newsletter, now()->addDay());
```

### Tracking Links and Opens

`InjectNewsletterTrackingAction` appends `utm_source`, `utm_medium` and `utm_campaign` query parameters to every external link in the rendered email, and appends an open-tracking pixel when `send_webview_link` is true. This runs automatically whenever `NewsletterMail` is built.

### Detecting Broken Links

```php
use JeffersonGoncalves\Newsletter\Actions\FindBrokenNewsletterLinksAction;

$broken = app(FindBrokenNewsletterLinksAction::class)->handle($newsletter);
// Collection of ['url' => ..., 'status' => int|null, 'error' => string|null]
```

## Troubleshooting

### Error: "This newsletter has already been sent."

**Cause**: `SendNewsletterAction` refuses to resend a newsletter whose `status` is already `sent`.

**Solution**: Create a new `Newsletter` record (or duplicate the existing one) instead of resending.

### Error: "The newsletter has no pending recipients to send to."

**Cause**: Every confirmed, non-unsubscribed member of the newsletter's email groups has already received it successfully, or no email groups have confirmed members yet.

**Solution**: Attach an `EmailGroup` with confirmed members, or check `NewsletterSentRecipient` for members that already received the newsletter.

## API Reference

### `Newsletter::send(Newsletter $newsletter): Newsletter`

Dispatches the batched send. Throws `InvalidArgumentException` if the newsletter was already sent, has an invalid `sender_email`, or has no pending recipients.

### `Newsletter::sendTest(Newsletter $newsletter, string $email): void`

Sends a synchronous preview copy without touching delivery tracking.

### `Newsletter::subscribe(string $email, ?string $groupTitle = null): EmailGroupMember`

Creates or reuses an `EmailGroupMember` and queues a confirmation email. Falls back to `config('newsletter.default_email_group')` when `$groupTitle` is null.

### `Newsletter::confirm(EmailGroupMember $emailGroupMember): EmailGroupMember`

Sets `confirmed_at` to now.

### `Newsletter::unsubscribe(EmailGroupMember $emailGroupMember): EmailGroupMember`

Sets `unsubscribed` to true and stamps `unsubscribed_at`.
