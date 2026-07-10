<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Newsletter\Actions\FindBrokenNewsletterLinksAction;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

it('finds broken links and unreachable urls in the newsletter content', function () {
    Http::fake([
        'https://ok.example.com/*' => Http::response('', 200),
        'https://broken.example.com/*' => Http::response('', 404),
        'https://timeout.example.com/*' => function () {
            throw new ConnectionException('Could not connect');
        },
    ]);

    $newsletter = Newsletter::factory()->create([
        'content' => '
            <a href="https://ok.example.com/page">ok</a>
            <a href="https://broken.example.com/page">broken</a>
            <img src="https://timeout.example.com/image.png">
        ',
    ]);

    $broken = app(FindBrokenNewsletterLinksAction::class)->handle($newsletter);

    expect($broken)->toHaveCount(2)
        ->and($broken->pluck('url')->all())->toEqualCanonicalizing([
            'https://broken.example.com/page',
            'https://timeout.example.com/image.png',
        ]);
});
