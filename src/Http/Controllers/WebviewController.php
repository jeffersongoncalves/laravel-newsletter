<?php

namespace JeffersonGoncalves\Newsletter\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

class WebviewController extends Controller
{
    public function show(string $route): View
    {
        $newsletter = Newsletter::query()
            ->where('route', $route)
            ->where('published', true)
            ->firstOrFail();

        return view('newsletter::webview', compact('newsletter'));
    }
}
