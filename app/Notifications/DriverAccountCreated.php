<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverAccountCreated extends Notification
{
    use Queueable;

    private $driver;
    private $password;

    public function __construct($driver, $password)
    {
        $this->driver = $driver;
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Driver Account Has Been Created')
            ->greeting('Hello ' . $this->driver->user->name . '!')
            ->line('Your driver account has been created by an administrator.')
            ->line('Here are your login credentials:')
            ->line('Email: ' . $this->driver->user->email)
            ->line('Password: ' . $this->password)
            ->line('License Number: ' . $this->driver->license_number)
            ->line('Phone: ' . $this->driver->phone_number)
            ->action('Login to Your Account', url('/login'))
            ->line('Please change your password after your first login for security.')
            ->line('Thank you for joining our team!');
    }
}