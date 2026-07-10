<?php

namespace JeffersonGoncalves\Newsletter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JeffersonGoncalves\Newsletter\Actions\SendNewsletterAction;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class SendScheduledNewslettersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(SendNewsletterAction $sendNewsletterAction): void
    {
        Newsletter::query()
            ->where('status', NewsletterStatus::Scheduled)
            ->where('scheduled_at', '<=', now())
            ->each(fn (Newsletter $newsletter) => $sendNewsletterAction->handle($newsletter));
    }
}
