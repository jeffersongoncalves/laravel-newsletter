<?php

namespace JeffersonGoncalves\Newsletter\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class ConfirmSubscriptionMail extends Mailable implements ShouldQueue
{
    use SerializesModels;

    public function __construct(
        public EmailGroupMember $emailGroupMember,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('newsletter::emails.confirm_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'newsletter::mail.confirm-subscription',
            with: [
                'emailGroupMember' => $this->emailGroupMember,
                'confirmUrl' => $this->confirmUrl(),
            ],
        );
    }

    public function confirmUrl(): string
    {
        return URL::temporarySignedRoute(
            'newsletter.confirm',
            now()->addDays(7),
            ['emailGroupMember' => $this->emailGroupMember->getKey()],
        );
    }
}
