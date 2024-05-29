<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $user;

    public function __construct($payment, $user) {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $token = md5($this->user->email . $this->payment->payment_id);
        return (new MailMessage)
            ->subject('Deposit Approval - ' . $this->payment->payment_id)
            ->greeting('Deposit Approval- ' . $this->payment->payment_id)
            ->line('Email: ' . $this->user->email)
            ->line('Name: ' . $this->user->first_name)
            ->line('Account No: ' . $this->payment->to)
            ->line('Deposit Amount: ' . $this->payment->amount)
            ->line('TxID: ' . $this->payment->TxID)
            ->line('Click the button to proceed with approval')
            ->action('Approval', url('https://login.qcgbrokertw.com/approval/' . $token))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
