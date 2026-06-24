<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

namespace App\Mail;

use App\Helpers\Validators;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Message $received;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Message  $received
     */
    public function __construct(Message $received)
    {
        $this->received = $received;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        $name = null;
        if (Validators::isMobileNumber($this->received->from)) {
            $contact = $this->received->user->contacts()->whereMobileNumber($this->received->from)->first();
            if ($contact)
            {
                $fields = $contact->fields->mapWithKeys(fn($field) => [$field->tag => $field->pivot->value]);
                $fields = array_change_key_case($fields);
                if (filled($fields['first_name'])) {
                    $name = $fields['first_name'];
                    if (filled($fields['last_name'])) {
                        $name .= " {$fields['last_name']}";
                    }
                } elseif (filled($fields['name'])) {
                    $name = $fields['name'];
                }
            }
        }

        if (empty($name)) {
            $name = $this->received->from;
        }

        return new Envelope(
            subject: __(
                'emails.messages.received.subject',
                ['from' => $name, 'to' => $this->received->to]
            ),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.messages.received',
            text: 'emails.messages.received-text',
            with: [
                'received' => $this->received,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return $this->received->getMedia('attachments')
                              ->map(function ($media) {
                                  return Attachment::fromPath($media->getPath())
                                                   ->as($media->file_name)
                                                   ->withMime($media->mime_type);
                              })
                              ->toArray();
    }
}
