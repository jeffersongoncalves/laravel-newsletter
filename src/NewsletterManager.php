<?php

namespace JeffersonGoncalves\Newsletter;

use JeffersonGoncalves\Newsletter\Actions\ConfirmEmailGroupSubscriptionAction;
use JeffersonGoncalves\Newsletter\Actions\SendNewsletterAction;
use JeffersonGoncalves\Newsletter\Actions\SendTestNewsletterAction;
use JeffersonGoncalves\Newsletter\Actions\SubscribeToEmailGroupAction;
use JeffersonGoncalves\Newsletter\Actions\UnsubscribeEmailGroupMemberAction;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class NewsletterManager
{
    public function __construct(
        protected SendNewsletterAction $sendNewsletterAction,
        protected SendTestNewsletterAction $sendTestNewsletterAction,
        protected SubscribeToEmailGroupAction $subscribeToEmailGroupAction,
        protected ConfirmEmailGroupSubscriptionAction $confirmEmailGroupSubscriptionAction,
        protected UnsubscribeEmailGroupMemberAction $unsubscribeEmailGroupMemberAction,
    ) {}

    public function send(Newsletter $newsletter): Newsletter
    {
        return $this->sendNewsletterAction->handle($newsletter);
    }

    public function sendTest(Newsletter $newsletter, string $email): void
    {
        $this->sendTestNewsletterAction->handle($newsletter, $email);
    }

    public function subscribe(string $email, ?string $groupTitle = null): EmailGroupMember
    {
        return $this->subscribeToEmailGroupAction->handle(
            $email,
            $groupTitle ?? config('newsletter.default_email_group'),
        );
    }

    public function confirm(EmailGroupMember $emailGroupMember): EmailGroupMember
    {
        return $this->confirmEmailGroupSubscriptionAction->handle($emailGroupMember);
    }

    public function unsubscribe(EmailGroupMember $emailGroupMember): EmailGroupMember
    {
        return $this->unsubscribeEmailGroupMemberAction->handle($emailGroupMember);
    }
}
