<?php

namespace JeffersonGoncalves\Newsletter\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use JeffersonGoncalves\Newsletter\Enums\RecipientDeliveryStatus;
use JeffersonGoncalves\Newsletter\Mail\NewsletterMail;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;
use JeffersonGoncalves\Newsletter\Models\NewsletterSentRecipient;
use Throwable;

class SendNewsletterEmailJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public string $email,
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->email)->sendNow(new NewsletterMail($this->newsletter, $this->resolveEmailGroupMember()));

            NewsletterSentRecipient::query()->create([
                'newsletter_id' => $this->newsletter->id,
                'email' => $this->email,
                'status' => RecipientDeliveryStatus::Sent,
                'sent_at' => now(),
            ]);
        } catch (Throwable $exception) {
            NewsletterSentRecipient::query()->create([
                'newsletter_id' => $this->newsletter->id,
                'email' => $this->email,
                'status' => RecipientDeliveryStatus::Failed,
                'error_message' => $exception->getMessage(),
            ]);
        }

        Newsletter::whereKey($this->newsletter->id)->increment('total_recipients');
    }

    protected function resolveEmailGroupMember(): ?EmailGroupMember
    {
        return EmailGroupMember::query()
            ->whereIn('email_group_id', $this->newsletter->emailGroups()->pluck('email_groups.id'))
            ->where('email', $this->email)
            ->first();
    }
}
