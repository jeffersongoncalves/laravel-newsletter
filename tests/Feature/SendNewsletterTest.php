<?php

use Illuminate\Support\Facades\Mail;
use JeffersonGoncalves\Newsletter\Actions\SendNewsletterAction;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Enums\RecipientDeliveryStatus;
use JeffersonGoncalves\Newsletter\Mail\NewsletterMail;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;
use JeffersonGoncalves\Newsletter\Models\Newsletter;
use JeffersonGoncalves\Newsletter\Models\NewsletterSentRecipient;

it('sends the newsletter to every confirmed subscriber and marks it as sent', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create();
    $group = EmailGroup::factory()->create();
    $newsletter->emailGroups()->attach($group);

    $members = EmailGroupMember::factory()->for($group)->count(3)->create();

    app(SendNewsletterAction::class)->handle($newsletter);

    $newsletter->refresh();

    expect($newsletter->status)->toBe(NewsletterStatus::Sent)
        ->and($newsletter->sent_at)->not->toBeNull()
        ->and(NewsletterSentRecipient::where('newsletter_id', $newsletter->id)->where('status', RecipientDeliveryStatus::Sent)->count())->toBe(3);

    foreach ($members as $member) {
        Mail::assertSent(NewsletterMail::class, fn (NewsletterMail $mail): bool => $mail->hasTo($member->email));
    }
});

it('ignores unsubscribed and unconfirmed members when sending', function () {
    Mail::fake();

    $newsletter = Newsletter::factory()->create();
    $group = EmailGroup::factory()->create();
    $newsletter->emailGroups()->attach($group);

    EmailGroupMember::factory()->for($group)->create();
    EmailGroupMember::factory()->for($group)->unconfirmed()->create();
    EmailGroupMember::factory()->for($group)->unsubscribed()->create();

    app(SendNewsletterAction::class)->handle($newsletter);

    expect(NewsletterSentRecipient::where('newsletter_id', $newsletter->id)->count())->toBe(1);
});

it('does not allow sending a newsletter that has already been sent', function () {
    $newsletter = Newsletter::factory()->sent()->create();

    app(SendNewsletterAction::class)->handle($newsletter);
})->throws(InvalidArgumentException::class, 'This newsletter has already been sent.');

it('does not allow sending a newsletter without pending recipients', function () {
    $newsletter = Newsletter::factory()->create();

    app(SendNewsletterAction::class)->handle($newsletter);
})->throws(InvalidArgumentException::class, 'The newsletter has no pending recipients to send to.');
