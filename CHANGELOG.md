# Changelog

All notable changes to this project will be documented in this file.

## 2.0.0 - 2026-08-24

### Breaking

- Drop Laravel 11 support. Requires `illuminate/*` ^12.0|^13.0 and `orchestra/testbench` ^10.0|^11.0.

### Added

- Filament-free subscribe page at `GET /newsletter/subscribe`: plain Blade + Tailwind (CDN) form, no panel dependency. Toggle with `NEWSLETTER_SUBSCRIBE_FORM_ENABLED`, customize by publishing `--tag=newsletter-views`.
- Restyled confirmed/unsubscribed pages to match, sharing a new `layout.blade.php`.

## v1.0.0 - 2026-07-14

### What's Changed

* fix: pin GitHub Actions to commit SHA by @jeffersongoncalves in https://github.com/jeffersongoncalves/laravel-newsletter/pull/1

### New Contributors

* @jeffersongoncalves made their first contribution in https://github.com/jeffersongoncalves/laravel-newsletter/pull/1

**Full Changelog**: https://github.com/jeffersongoncalves/laravel-newsletter/commits/v1.0.0
