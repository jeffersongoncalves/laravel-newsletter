<?php

namespace JeffersonGoncalves\Newsletter\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\Newsletter\NewsletterManager;

/**
 * @method static \JeffersonGoncalves\Newsletter\Models\Newsletter send(\JeffersonGoncalves\Newsletter\Models\Newsletter $newsletter)
 * @method static void sendTest(\JeffersonGoncalves\Newsletter\Models\Newsletter $newsletter, string $email)
 * @method static \JeffersonGoncalves\Newsletter\Models\EmailGroupMember subscribe(string $email, ?string $groupTitle = null)
 * @method static \JeffersonGoncalves\Newsletter\Models\EmailGroupMember confirm(\JeffersonGoncalves\Newsletter\Models\EmailGroupMember $emailGroupMember)
 * @method static \JeffersonGoncalves\Newsletter\Models\EmailGroupMember unsubscribe(\JeffersonGoncalves\Newsletter\Models\EmailGroupMember $emailGroupMember)
 *
 * @see NewsletterManager
 */
class Newsletter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NewsletterManager::class;
    }
}
