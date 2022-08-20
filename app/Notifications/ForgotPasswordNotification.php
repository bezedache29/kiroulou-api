<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ForgotPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('KiRoulOu - Renouvellement de votre mot de passe')
            ->greeting('Bonjour,')
            ->line('Vous avez demandé à réinitialiser votre mot de passe. Entrer le code ci-dessous dans l\'application.')
            // ->action('Réinitialiser mon mot de passe', route('reset.password', ['token' => $this->token]))
            ->line(new HtmlString('<strong><center>' . $this->token . '</center></strong>'))
            ->line('P.S. : Si vous n\'êtes pas à l\'initiative de cette demande, nous vous prions de ne pas tenir compte de cet email.')
            ->line('A bientôt,')
            ->salutation('L\'équipe KiRoulOu');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
