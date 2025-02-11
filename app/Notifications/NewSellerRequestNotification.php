<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSellerRequestNotification extends Notification
{
    use Queueable;

    public $sellerRequest;
    /**
     * Create a new notification instance.
     */
    public function __construct($sellerRequest)
    {
        $this->sellerRequest = $sellerRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New seller request')
            ->greeting('Hello')
            ->line('A new seller request has been submitted by ' . $this->sellerRequest->buyer->name . ' to become a seller.')
            ->action('View request', url('/admin/seller-requests/' . $this->sellerRequest->id))
            ->line('Please review it and approve or reject.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
