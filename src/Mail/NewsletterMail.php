<?php

namespace JeffersonGoncalves\Newsletter\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use JeffersonGoncalves\Newsletter\Actions\InjectNewsletterTrackingAction;
use JeffersonGoncalves\Newsletter\Enums\NewsletterContentType;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class NewsletterMail extends Mailable implements ShouldQueue
{
    use SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public ?EmailGroupMember $emailGroupMember = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->newsletter->sender_email !== ''
                ? new Address($this->newsletter->sender_email, $this->newsletter->sender_name ?? '')
                : null,
            subject: $this->newsletter->subject,
        );
    }

    public function content(): Content
    {
        $content = app(InjectNewsletterTrackingAction::class)->handle($this->newsletter->content, $this->newsletter);

        $data = [
            'content' => $content,
            'newsletter' => $this->newsletter,
            'unsubscribeUrl' => $this->unsubscribeUrl(),
        ];

        return $this->newsletter->content_type === NewsletterContentType::Html
            ? new Content(view: 'newsletter::mail.html', with: $data)
            : new Content(markdown: 'newsletter::mail.markdown', with: $data);
    }

    public function unsubscribeUrl(): ?string
    {
        if (! $this->newsletter->send_unsubscribe_link || ! $this->emailGroupMember) {
            return null;
        }

        return URL::temporarySignedRoute(
            'newsletter.unsubscribe',
            now()->addDays(30),
            ['emailGroupMember' => $this->emailGroupMember->getKey()],
        );
    }
}
