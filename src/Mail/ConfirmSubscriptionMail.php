<?php

/**
 * Newsletter - Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Mail;

use Contensio\Newsletter\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $confirmUrl;

    public function __construct(
        public readonly Subscriber $subscriber,
        public readonly array      $config,
        public readonly string     $fromName,
        public readonly string     $fromEmail,
    ) {
        $this->confirmUrl = route('contensio-newsletter.confirm', $subscriber->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($this->fromEmail, $this->fromName),
            subject: 'Confirm your subscription',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'contensio-newsletter::emails.confirm-subscription',
        );
    }
}
