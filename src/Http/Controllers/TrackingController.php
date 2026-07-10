<?php

namespace JeffersonGoncalves\Newsletter\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class TrackingController extends Controller
{
    /**
     * Base64 of a 1x1 transparent GIF.
     */
    private const PIXEL = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7';

    public function open(Newsletter $newsletter): Response
    {
        Newsletter::whereKey($newsletter->id)->increment('total_views');

        return response(base64_decode(self::PIXEL), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
