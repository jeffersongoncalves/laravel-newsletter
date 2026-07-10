<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use JeffersonGoncalves\Newsletter\Events\EmailGroupMemberUnsubscribed;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class UnsubscribeEmailGroupMemberAction
{
    public function handle(EmailGroupMember $emailGroupMember): EmailGroupMember
    {
        $emailGroupMember->update([
            'unsubscribed' => true,
            'unsubscribed_at' => now(),
        ]);

        EmailGroupMemberUnsubscribed::dispatch($emailGroupMember);

        return $emailGroupMember;
    }
}
