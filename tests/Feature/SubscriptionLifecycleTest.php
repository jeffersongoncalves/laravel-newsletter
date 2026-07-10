<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use JeffersonGoncalves\Newsletter\Actions\SubscribeToEmailGroupAction;
use JeffersonGoncalves\Newsletter\Mail\ConfirmSubscriptionMail;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

it('subscribes, confirms and unsubscribes an email group member through signed routes', function () {
    Mail::fake();

    $member = app(SubscribeToEmailGroupAction::class)->handle('subscriber@example.com', 'Weekly Digest');

    expect(EmailGroup::where('title', 'Weekly Digest')->exists())->toBeTrue()
        ->and($member->confirmed_at)->toBeNull();

    Mail::assertQueued(ConfirmSubscriptionMail::class);

    $confirmUrl = URL::temporarySignedRoute('newsletter.confirm', now()->addDays(7), ['emailGroupMember' => $member->id]);

    $this->get($confirmUrl)->assertOk();

    expect($member->fresh()->confirmed_at)->not->toBeNull();

    $unsubscribeUrl = URL::temporarySignedRoute('newsletter.unsubscribe', now()->addDays(30), ['emailGroupMember' => $member->id]);

    $this->get($unsubscribeUrl)->assertOk();

    $member->refresh();

    expect($member->unsubscribed)->toBeTrue()
        ->and($member->unsubscribed_at)->not->toBeNull();
});

it('rejects a tampered confirmation link', function () {
    $member = EmailGroupMember::factory()->unconfirmed()->create();

    $confirmUrl = URL::temporarySignedRoute('newsletter.confirm', now()->addDays(7), ['emailGroupMember' => $member->id]);

    $this->get($confirmUrl.'&tampered=1')->assertForbidden();

    expect($member->fresh()->confirmed_at)->toBeNull();
});

it('rejects an expired confirmation link', function () {
    $member = EmailGroupMember::factory()->unconfirmed()->create();

    $confirmUrl = URL::temporarySignedRoute('newsletter.confirm', now()->subMinute(), ['emailGroupMember' => $member->id]);

    $this->get($confirmUrl)->assertForbidden();

    expect($member->fresh()->confirmed_at)->toBeNull();
});
