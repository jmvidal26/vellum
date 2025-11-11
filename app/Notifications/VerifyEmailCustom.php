<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailCustom extends VerifyEmailBase implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Gera a URL de verificação segura (lógica herdada)
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Bem-vindo(a) ao Vellum!')

            ->greeting('Olá!')

            ->line('Bem-vindo(a) ao Vellum. Clique no botão abaixo para verificar seu endereço de e-mail e ativar sua conta no nosso refúgio literário.')

            ->action('Confirmar meu E-mail', $verificationUrl)

            ->line('Se você não criou esta conta, nenhuma ação é necessária.')

            ->salutation('Atenciosamente, Equipe Vellum');

    }
}
