<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $claim;

    public function __construct($message, $claim)
    {
        $this->message = $message;
        $this->claim = $claim;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You have a new message regarding your claim.')
            ->action('View Message', url('/claims/' . $this->claim->id . '/messages'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'claim_id' => $this->claim->id,
            'claim_title' => $this->claim->donation->title,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'claim_id' => $this->claim->id,
            'claim_title' => $this->claim->donation->title,
        ]);
    }
}
