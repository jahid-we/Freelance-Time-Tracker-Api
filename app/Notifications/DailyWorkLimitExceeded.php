<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyWorkLimitExceeded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $date;

    protected $hours;

    /**
     * Create a new notification instance.
     */
    public function __construct($date, $hours)
    {
        $this->date = $date;
        $this->hours = $hours;
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
            ->subject('Daily Work Limit Exceeded')
            ->greeting('Hello '.$notifiable->name)
            ->line("You've logged {$this->hours} hours on {$this->date}.")
            ->line('Please take care of your work-life balance.');
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
