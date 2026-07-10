<?php

use JeffersonGoncalves\Newsletter\Models\Newsletter;

it('increments total_views each time the tracking pixel is requested', function () {
    $newsletter = Newsletter::factory()->create(['total_views' => 0]);

    $response = $this->get(route('newsletter.track.open', $newsletter));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toBe('image/gif')
        ->and($newsletter->fresh()->total_views)->toBe(1);

    $this->get(route('newsletter.track.open', $newsletter));

    expect($newsletter->fresh()->total_views)->toBe(2);
});
