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

namespace App\Notifications;

use App\Mail\MessageReceived as MessageReceivedMailable;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class MessageReceived extends Notification
{
    use Queueable;

    private Message $received;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $received)
    {
        $this->received = $received;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        if (config('messaging.message_to_email') && Setting::retrieve('messaging.message_to_email', $notifiable->id, false)) {
            $credits = config('saas.credits.message_to_email.amount');

            if ($notifiable->consume($credits)) {
                return ['mail'];
            }
        }

        return [];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MessageReceivedMailable
    {
        return (new MessageReceivedMailable($this->received))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
