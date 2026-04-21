<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $role = 'bettor') {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'SCOUT');

        if ($this->role === 'tipster') {
            return (new MailMessage)
                ->subject("Welcome to {$appName} — Tipster Application Received")
                ->greeting("Hi {$notifiable->name}!")
                ->line("Thank you for applying to become a tipster on {$appName}.")
                ->line("Your application is currently under review. Our admin team will assess your profile and notify you once a decision has been made.")
                ->line("In the meantime, feel free to explore the platform as a regular member.")
                ->action('Visit ' . $appName, url('/'))
                ->line('Please gamble responsibly. 18+ only.');
        }

        return (new MailMessage)
            ->subject("Welcome to {$appName} — Your Account is Ready")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Welcome to {$appName} — AI-powered football betting tips updated daily.")
            ->line("You now have access to high-confidence AI predictions, value bet signals, and expert match analysis.")
            ->action('See Today\'s Tips', route('home'))
            ->line('Please gamble responsibly. 18+ only. T&Cs apply.');
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->role === 'tipster') {
            return [
                'type'    => 'welcome_tipster',
                'title'   => 'Application Submitted',
                'message' => 'Your tipster application is under review. We\'ll notify you once it has been approved.',
                'action_url' => route('dashboard'),
            ];
        }

        return [
            'type'    => 'welcome_bettor',
            'title'   => 'Welcome to ' . config('app.name', 'SCOUT') . '!',
            'message' => 'Your account is ready. Check out today\'s AI-powered football tips.',
            'action_url' => route('home'),
        ];
    }
}
