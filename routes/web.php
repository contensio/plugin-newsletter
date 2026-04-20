<?php

use Contensio\Newsletter\Http\Controllers\Admin\SettingsController;
use Contensio\Newsletter\Http\Controllers\Admin\SubscriberController;
use Contensio\Newsletter\Http\Controllers\Frontend\SubscribeController;
use Illuminate\Support\Facades\Route;

// ── Admin routes ─────────────────────────────────────────────────────────────

Route::prefix(config('contensio.route_prefix', 'account'))
    ->middleware(['web', 'contensio.auth', 'contensio.admin'])
    ->group(function () {

        // Subscriber list + export
        Route::get('/newsletter',           [SubscriberController::class, 'index'])  ->name('contensio-newsletter.subscribers');
        Route::get('/newsletter/export',    [SubscriberController::class, 'export']) ->name('contensio-newsletter.export');
        Route::delete('/newsletter/{id}',   [SubscriberController::class, 'destroy'])->name('contensio-newsletter.subscriber.delete');

        // Settings
        Route::get('/settings/newsletter',  [SettingsController::class, 'index'])->name('contensio-newsletter.settings');
        Route::post('/settings/newsletter', [SettingsController::class, 'save']) ->name('contensio-newsletter.settings.save');
    });

// ── Public routes ─────────────────────────────────────────────────────────────

Route::middleware('web')->group(function () {
    Route::post('/newsletter/subscribe',          [SubscribeController::class, 'subscribe'])  ->name('contensio-newsletter.subscribe');
    Route::get('/newsletter/confirm/{token}',     [SubscribeController::class, 'confirm'])    ->name('contensio-newsletter.confirm');
    Route::get('/newsletter/unsubscribe/{token}', [SubscribeController::class, 'unsubscribe'])->name('contensio-newsletter.unsubscribe');
});
