<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessful extends Notification
{
    use Queueable;

    public $payment;
    /**
     * Create a new notification instance.
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('The payment was completed successfully🎉')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The payment was completed successfully for amount $' . $this->payment->amount)
            ->line('Payment ID: ' . $this->payment->payment_id)
            ->line('Thank you for using our application! 😊')
            ->action('View the order', url('/orders/' . $this->payment->id))
            ->line('If you think this message reached you by mistake, please ignore it.');
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
