<?php

/**
 * Newsletter - Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Http\Controllers\Frontend;

use Contensio\Newsletter\Mail\ConfirmSubscriptionMail;
use Contensio\Newsletter\Models\Subscriber;
use Contensio\Newsletter\Support\NewsletterConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class SubscribeController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:150',
        ]);

        $email  = strtolower(trim($request->input('email')));
        $name   = strip_tags(trim($request->input('name', '')));
        $config = NewsletterConfig::all();

        $existing = Subscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->isActive()) {
                // Already subscribed - return success silently to prevent email enumeration
                return $this->successResponse($request, $config);
            }

            if ($existing->isPending()) {
                // Resend confirmation
                if ($config['double_optin']) {
                    $this->sendConfirmation($existing, $config);
                }
                return $this->successResponse($request, $config);
            }

            // Was unsubscribed - reactivate
            $existing->update([
                'name'             => $name ?: $existing->name,
                'status'           => $config['double_optin'] ? Subscriber::STATUS_PENDING : Subscriber::STATUS_ACTIVE,
                'confirmed_at'     => $config['double_optin'] ? null : now(),
                'unsubscribed_at'  => null,
                'ip_address'       => $request->ip(),
            ]);

            if ($config['double_optin']) {
                $this->sendConfirmation($existing, $config);
            }

            return $this->successResponse($request, $config);
        }

        // New subscriber
        $subscriber = Subscriber::create([
            'email'      => $email,
            'name'       => $name ?: null,
            'token'      => Subscriber::generateToken(),
            'status'     => $config['double_optin'] ? Subscriber::STATUS_PENDING : Subscriber::STATUS_ACTIVE,
            'confirmed_at' => $config['double_optin'] ? null : now(),
            'source'     => 'widget',
            'ip_address' => $request->ip(),
        ]);

        if ($config['double_optin']) {
            $this->sendConfirmation($subscriber, $config);
        }

        return $this->successResponse($request, $config);
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)
            ->where('status', Subscriber::STATUS_PENDING)
            ->first();

        if (! $subscriber) {
            return view('contensio-newsletter::public.confirm', ['success' => false]);
        }

        $subscriber->confirm();

        return view('contensio-newsletter::public.confirm', ['success' => true, 'subscriber' => $subscriber]);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        if (! $subscriber) {
            return view('contensio-newsletter::public.unsubscribe', ['success' => false]);
        }

        if ($subscriber->isActive() || $subscriber->isPending()) {
            $subscriber->unsubscribe();
        }

        return view('contensio-newsletter::public.unsubscribe', ['success' => true]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function sendConfirmation(Subscriber $subscriber, array $config): void
    {
        try {
            $fromEmail = $config['from_email'] ?: config('mail.from.address');
            $fromName  = $config['from_name']  ?: config('mail.from.name');

            Mail::to($subscriber->email, $subscriber->name ?: null)
                ->send(new ConfirmSubscriptionMail($subscriber, $config, $fromName, $fromEmail));
        } catch (\Throwable) {
            // Never block a subscription because email failed
        }
    }

    private function successResponse(Request $request, array $config)
    {
        $message = $config['double_optin']
            ? $config['success_message']
            : $config['success_message_no_optin'];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('newsletter_success', $message);
    }
}
