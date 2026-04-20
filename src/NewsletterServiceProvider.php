<?php

/**
 * Newsletter - Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter;

use Contensio\Newsletter\Support\NewsletterConfig;
use Contensio\Support\Hook;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected string $ns = 'contensio-newsletter';

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', $this->ns);
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Settings card on the admin settings hub
        Hook::add('contensio/admin/settings-cards', function () {
            return view($this->ns . '::partials.settings-hub-card')->render();
        });

        // Widget: signup form in the "newsletter" widget area
        // Themes can also call WidgetArea::render('newsletter') directly
        Hook::add('contensio/frontend/body-end', function () {
            // No global injection - form is embedded via widget or shortcode
            return '';
        });
    }
}
