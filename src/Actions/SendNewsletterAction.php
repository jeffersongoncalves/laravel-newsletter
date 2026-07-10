<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use InvalidArgumentException;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Events\NewsletterSent;
use JeffersonGoncalves\Newsletter\Jobs\SendNewsletterEmailJob;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class SendNewsletterAction
{
    public function __construct(
        protected ResolveNewsletterRecipientsAction $resolveNewsletterRecipients,
    ) {}

    public function handle(Newsletter $newsletter): Newsletter
    {
        if ($newsletter->status === NewsletterStatus::Sent) {
            throw new InvalidArgumentException('This newsletter has already been sent.');
        }

        if (! filter_var($newsletter->sender_email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The newsletter sender_email is not a valid email address.');
        }

        $recipients = $this->resolveNewsletterRecipients->handle($newsletter);

        if ($recipients->isEmpty()) {
            throw new InvalidArgumentException('The newsletter has no pending recipients to send to.');
        }

        $newsletter->update(['status' => NewsletterStatus::Sending]);

        $jobs = $recipients
            ->map(fn (string $email): SendNewsletterEmailJob => new SendNewsletterEmailJob($newsletter, $email))
            ->all();

        Bus::batch($jobs)
            ->finally(function (Batch $batch) use ($newsletter): void {
                $newsletter->update([
                    'status' => NewsletterStatus::Sent,
                    'sent_at' => now(),
                ]);

                NewsletterSent::dispatch($newsletter->fresh() ?? $newsletter);
            })
            ->dispatch();

        return $newsletter;
    }
}
