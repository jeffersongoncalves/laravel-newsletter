<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use DateTimeInterface;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Events\NewsletterScheduled;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

/**
 * Not part of the original action set, added so the documented
 * NewsletterScheduled event has a concrete place to be dispatched from.
 */
class ScheduleNewsletterAction
{
    public function handle(Newsletter $newsletter, DateTimeInterface $scheduledAt): Newsletter
    {
        $newsletter->update([
            'status' => NewsletterStatus::Scheduled,
            'scheduled_at' => $scheduledAt,
        ]);

        NewsletterScheduled::dispatch($newsletter);

        return $newsletter;
    }
}
