<?php

/**
 * Newsletter - Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Http\Controllers\Admin;

use Contensio\Newsletter\Support\NewsletterConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $config = NewsletterConfig::all();

        return view('contensio-newsletter::admin.settings', compact('config'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'from_name'         => 'nullable|string|max:100',
            'from_email'        => 'nullable|email|max:255',
            'form_title'        => 'required|string|max:120',
            'form_description'  => 'nullable|string|max:300',
            'form_placeholder'  => 'required|string|max:80',
            'form_button'       => 'required|string|max:60',
            'success_message'   => 'required|string|max:300',
            'success_message_no_optin' => 'required|string|max:300',
        ]);

        NewsletterConfig::save($request->all());

        return redirect()->route('contensio-newsletter.settings')->with('success', 'Newsletter settings saved.');
    }
}
