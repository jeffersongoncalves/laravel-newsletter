<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use Illuminate\Support\Facades\Mail;
use JeffersonGoncalves\Newsletter\Mail\NewsletterMail;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class SendTestNewsletterAction
{
    public function handle(Newsletter $newsletter, string $email): void
    {
        Mail::to($email)->sendNow(new NewsletterMail($newsletter));
    }
}
