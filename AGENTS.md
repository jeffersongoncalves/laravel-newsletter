# AGENTS.md

Instructions for AI coding agents working in this repository.

## Project

`jeffersongoncalves/laravel-newsletter` — compose, schedule and send double opt-in email newsletters with open/click tracking for Laravel applications. No Filament dependency; consumed via its `Newsletter` facade, models, or the built-in public subscribe/confirm/unsubscribe routes. See [README.md](README.md) for the full feature list and [resources/boost/skills/newsletter-development/SKILL.md](resources/boost/skills/newsletter-development/SKILL.md) for a deeper development reference.

## Setup

```bash
composer install
```

## Commands

```bash
composer test      # Pest test suite
composer analyse    # PHPStan (Larastan) level 5
composer format     # Pint (Laravel preset)
```

Run all three (`test`, `analyse`, `format`) before considering a change done — CI enforces all three on every push.

## Conventions

- **Namespace**: everything under `JeffersonGoncalves\Newsletter\`; tests under `JeffersonGoncalves\Newsletter\Tests\`.
- **Actions, not fat models/controllers.** Each mutation (subscribe, confirm, unsubscribe, schedule, send, send a test) is a single-purpose invokable class under `src/Actions/`. Controllers and the `NewsletterManager`/`Newsletter` facade just call into these — don't inline business logic in a controller.
- **Public routes require no auth.** `subscribe` (POST), `confirm/{emailGroupMember}` and `unsubscribe/{emailGroupMember}` (signed GET) are meant to be hit directly by end users from an email link — never add auth middleware to them. The `GET subscribe` form route is gated by `config('newsletter.subscribe_form_enabled')` and can be disabled by apps that ship their own form.
- **Views have no Filament/panel dependency.** `subscribe`, `confirmed`, `unsubscribed` and the shared `layout` Blade views are plain Blade + Tailwind (via CDN) so the package works in any Laravel app. Keep them dependency-free and publishable (`vendor:publish --tag=newsletter-views`).
- **Double opt-in is not optional.** `EmailGroupMember.confirmed_at` stays null until the signed confirm link is clicked; `SendNewsletterAction`/`ResolveNewsletterRecipientsAction` must only ever resolve confirmed, non-unsubscribed members. Don't add a path that sends to an unconfirmed member.
- **Tracking is a pure feature flag** (`newsletter.tracking_enabled`) — a complete no-op when off. `InjectNewsletterTrackingAction` runs automatically whenever `NewsletterMail` is built; don't special-case call sites.
- **PHP 8.2+, Laravel 12/13.** Don't add code that only works on one supported major without a compatibility check.

## Testing notes

- Pest 3/4 on Orchestra Testbench, SQLite in-memory. `tests/TestCase.php` registers the package provider and configures the test environment.
- Confirm/unsubscribe routes use Laravel's `signed` middleware — tests hitting them must generate a signed URL (`URL::signedRoute` / `->confirmUrl()`-style helpers on the model), not a bare route call.
- `SendNewsletterAction` dispatches a `Bus::batch()` — use `Bus::fake()` (or let the batch run synchronously in tests) rather than asserting on queued jobs directly.
- Never edit `CHANGELOG.md` by hand — `.github/workflows/update-changelog.yml` populates it from GitHub Releases, and it only fires once per release.

## Commit style

Conventional commits (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`, `ci:`), English, explaining *why* over *what*. Primary branch is `master`.
