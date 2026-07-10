<?php

use Illuminate\Support\Facades\Mail;
use JeffersonGoncalves\Newsletter\Actions\SendNewsletterAction;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Jobs\SendScheduledNewslettersJob;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

it('only sends scheduled newsletters that are due', function () {
    Mail::fake();

    $group = EmailGroup::factory()->create();
    EmailGroupMember::factory()->for($group)->create();

    $due = Newsletter::factory()->scheduled()->create(['scheduled_at' => now()->subMinute()]);
    $due->emailGroups()->attach($group);

    $notYetDue = Newsletter::factory()->scheduled()->create(['scheduled_at' => now()->addDay()]);
    $notYetDue->emailGroups()->attach($group);

    app(SendScheduledNewslettersJob::class)->handle(app(SendNewsletterAction::class));

    expect($due->fresh()->status)->toBe(NewsletterStatus::Sent)
        ->and($notYetDue->fresh()->status)->toBe(NewsletterStatus::Scheduled);
});
