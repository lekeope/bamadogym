<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $type,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $membership = $notifiable->memberships()->latest()->first();
        $renewalDate = $membership?->renewal_date?->format('M j, Y') ?? 'unknown';
        $renewUrl = url('/member');

        return match ($this->type) {
            'due_soon' => (new MailMessage)
                ->subject('Your Bamado Gym membership renews in 7 days')
                ->greeting('Hi ' . $notifiable->name . '!')
                ->line('Your membership is due for renewal on ' . $renewalDate . '.')
                ->action('Renew Now', $renewUrl)
                ->line('Come in to the desk or renew online to keep your access uninterrupted.'),

            'due_today' => (new MailMessage)
                ->subject('Your Bamado Gym membership expires today')
                ->greeting('Hi ' . $notifiable->name . '!')
                ->line('Your membership expires today (' . $renewalDate . ').')
                ->action('Renew Now', $renewUrl)
                ->line('Renew now to avoid losing access.'),

            'overdue_3' => (new MailMessage)
                ->subject('Your Bamado Gym membership is overdue')
                ->greeting('Hi ' . $notifiable->name . '!')
                ->line('Your membership expired 3 days ago. You will not be able to check in until you renew.')
                ->action('Renew Now', $renewUrl),

            'overdue_7' => (new MailMessage)
                ->subject('Action required: Bamado Gym membership overdue')
                ->greeting('Hi ' . $notifiable->name . '!')
                ->line('Your membership has been overdue for 7 days. Please renew as soon as possible.')
                ->action('Contact Us', url('/'))
                ->line('Visit the gym or contact us to reinstate your membership.'),

            default => (new MailMessage)
                ->subject('Reminder from Bamado Gym')
                ->greeting('Hi ' . $notifiable->name . '!')
                ->line('This is a friendly reminder from Bamado Gym.')
                ->action('Visit Member Portal', $renewUrl),
        };
    }

    public static function sendToMember(User $user, string $type): void
    {
        $user->notify(new self($type));
    }
}
