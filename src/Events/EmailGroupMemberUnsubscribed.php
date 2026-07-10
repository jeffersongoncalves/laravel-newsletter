<?php

namespace JeffersonGoncalves\Newsletter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class EmailGroupMemberUnsubscribed
{
    use Dispatchable;

    public function __construct(
        public EmailGroupMember $emailGroupMember,
    ) {}
}
