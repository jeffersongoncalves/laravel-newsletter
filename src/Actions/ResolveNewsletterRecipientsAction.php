<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use Illuminate\Support\Collection;
use JeffersonGoncalves\Newsletter\Enums\RecipientDeliveryStatus;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class ResolveNewsletterRecipientsAction
{
    /**
     * @return Collection<int, string>
     */
    public function handle(Newsletter $newsletter): Collection
    {
        $groupIds = $newsletter->emailGroups()->pluck('email_groups.id');

        $alreadySent = $newsletter->sentRecipients()
            ->where('status', RecipientDeliveryStatus::Sent)
            ->pluck('email');

        return EmailGroupMember::query()
            ->whereIn('email_group_id', $groupIds)
            ->where('unsubscribed', false)
            ->whereNotNull('confirmed_at')
            ->whereNotIn('email', $alreadySent)
            ->distinct()
            ->pluck('email');
    }
}
