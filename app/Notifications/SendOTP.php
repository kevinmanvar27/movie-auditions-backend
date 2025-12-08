<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendOTP extends Notification
{
    protected $otpCode;
    protected $type; // 'registration' or 'forgot_password'

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otpCode, $type = 'registration')
    {
        $this->otpCode = $otpCode;
        $this->type = $type;
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
        if ($this->type === 'forgot_password') {
            return (new MailMessage)
                ->subject('Password Reset OTP')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->line('Your OTP code is: ' . $this->otpCode)
                ->line('This OTP code will expire in 10 minutes.')
                ->line('If you did not request a password reset, no further action is required.');
        } else {
            return (new MailMessage)
                ->subject('Email Verification OTP')
                ->line('Thank you for registering with us.')
                ->line('Your OTP code for email verification is: ' . $this->otpCode)
                ->line('This OTP code will expire in 10 minutes.')
                ->line('If you did not create an account, no further action is required.');
        }
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
            'otp_code' => $this->otpCode,
            'type' => $this->type,
        ];
    }
}