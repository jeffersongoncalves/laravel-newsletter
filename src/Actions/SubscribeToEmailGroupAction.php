<?php

namespace JeffersonGoncalves\Newsletter\Actions;

use Illuminate\Support\Facades\Mail;
use JeffersonGoncalves\Newsletter\Events\EmailGroupMemberSubscribed;
use JeffersonGoncalves\Newsletter\Mail\ConfirmSubscriptionMail;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class SubscribeToEmailGroupAction
{
    public function handle(string $email, string $groupTitle): EmailGroupMember
    {
        $emailGroup = EmailGroup::query()->firstOrCreate(['title' => $groupTitle]);

        $emailGroupMember = EmailGroupMember::query()->firstOrCreate(
            [
                'email_group_id' => $emailGroup->id,
                'email' => $email,
            ],
            [
                'confirmed_at' => null,
            ],
        );

        Mail::to($emailGroupMember->email)->queue(new ConfirmSubscriptionMail($emailGroupMember));

        EmailGroupMemberSubscribed::dispatch($emailGroupMember);

        return $emailGroupMember;
    }
}
