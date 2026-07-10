<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use JeffersonGoncalves\Newsletter\Http\Controllers\SubscriptionController;
use JeffersonGoncalves\Newsletter\Http\Controllers\TrackingController;
use JeffersonGoncalves\Newsletter\Http\Controllers\WebviewController;

Route::prefix(config('newsletter.route_prefix', 'newsletter'))
    ->name('newsletter.')
    ->middleware(SubstituteBindings::class)
    ->group(function (): void {
        Route::post('subscribe', [SubscriptionController::class, 'subscribe'])
            ->middleware('throttle:6,1')
            ->name('subscribe');

        Route::get('confirm/{emailGroupMember}', [SubscriptionController::class, 'confirm'])
            ->middleware('signed')
            ->name('confirm');

        Route::get('unsubscribe/{emailGroupMember}', [SubscriptionController::class, 'unsubscribe'])
            ->middleware('signed')
            ->name('unsubscribe');

        Route::get('track/{newsletter}/open.gif', [TrackingController::class, 'open'])
            ->name('track.open');

        Route::get('{route}', [WebviewController::class, 'show'])
            ->name('webview');
    });
