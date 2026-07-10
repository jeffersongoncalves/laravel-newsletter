<?php

namespace JeffersonGoncalves\Newsletter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class NewsletterSent
{
    use Dispatchable;

    public function __construct(
        public Newsletter $newsletter,
    ) {}
}
