<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TourAdminRequestNotification extends Notification
{
    use Queueable;

    public $user;
    public $tour;

    public function __construct($user, $tour)
    {
        $this->user = $user;
        $this->tour = $tour;
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

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tour Admin Request')
            ->greeting("Hello {$notifiable->name},")
            ->line("User {$this->user->name} ({$this->user->email}) is requesting to become a tour admin.")
            ->line("Tour Information:")
            ->line("• Tour Name: {$this->tour->name}")
            ->line("• Destination: {$this->tour->destination}")
            ->line("• Start Date: {$this->tour->start_date}")
            ->line("• End Date: {$this->tour->end_date}")
            ->line("Please review this request and take appropriate action.")
            ->line('Thank you.');
    }


}
