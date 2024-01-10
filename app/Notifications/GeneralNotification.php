<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    /*
for database notification
php artisan notifications:table
php artisan migrate

php artisan make:notification InvoicePaid (app/Notifications/)
    'mail' => 'database'
    delete toMail function
    toArray => toDatabase
    */
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public string $message,
        public string $sourceable_id,
        public string $sourceable_type,
        public string $web_link,
        public array $deep_link
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }



    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'sourceable_id' => $this->sourceable_id,
            'sourceable_type' => $this->sourceable_type,
            'web_link' => $this->web_link
        ];
    }
}
