<?php

/**
 * Newsletter — Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Support;

use Contensio\Models\Setting;

class NewsletterConfig
{
    protected static array $defaults = [
        'double_optin'      => true,
        'from_name'         => '',
        'from_email'        => '',
        'form_title'        => 'Stay in the loop',
        'form_description'  => 'Get the latest articles and updates delivered to your inbox.',
        'form_placeholder'  => 'Your email address',
        'form_button'       => 'Subscribe',
        'success_message'   => 'Thanks! Please check your inbox to confirm your subscription.',
        'success_message_no_optin' => 'You\'re subscribed! Welcome aboard.',
    ];

    public static function all(): array
    {
        try {
            $raw = Setting::where('module', 'newsletter')
                ->where('setting_key', 'config')
                ->value('value');

            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return array_merge(static::$defaults, $decoded);
                }
            }
        } catch (\Throwable) {}

        return static::$defaults;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()[$key] ?? $default ?? (static::$defaults[$key] ?? null);
    }

    public static function save(array $values): void
    {
        $stored = [
            'double_optin'      => (bool) ($values['double_optin']     ?? false),
            'from_name'         => strip_tags(trim($values['from_name']  ?? '')),
            'from_email'        => filter_var(trim($values['from_email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'form_title'        => strip_tags(trim($values['form_title']       ?? static::$defaults['form_title'])),
            'form_description'  => strip_tags(trim($values['form_description'] ?? static::$defaults['form_description'])),
            'form_placeholder'  => strip_tags(trim($values['form_placeholder'] ?? static::$defaults['form_placeholder'])),
            'form_button'       => strip_tags(trim($values['form_button']       ?? static::$defaults['form_button'])),
            'success_message'   => strip_tags(trim($values['success_message']   ?? static::$defaults['success_message'])),
            'success_message_no_optin' => strip_tags(trim($values['success_message_no_optin'] ?? static::$defaults['success_message_no_optin'])),
        ];

        Setting::updateOrCreate(
            ['module' => 'newsletter', 'setting_key' => 'config'],
            ['value' => json_encode($stored, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]
        );
    }
}
