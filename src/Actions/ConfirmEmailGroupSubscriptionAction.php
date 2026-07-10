<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use JeffersonGoncalves\Newsletter\Events\EmailGroupMemberConfirmed;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class ConfirmEmailGroupSubscriptionAction
{
    public function handle(EmailGroupMember $emailGroupMember): EmailGroupMember
    {
        $emailGroupMember->update(['confirmed_at' => now()]);

        EmailGroupMemberConfirmed::dispatch($emailGroupMember);

        return $emailGroupMember;
    }
}
