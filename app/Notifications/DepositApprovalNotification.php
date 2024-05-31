<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $user = User::find($this->payment->user_id);
        $token = md5($user->email . $this->payment->payment_id);

        return (new MailMessage)
            ->subject('Deposit Approval - ' . $this->payment->payment_id)
            ->greeting('Deposit Approval- ' . $this->payment->payment_id)
            ->line('Email: ' . $user->email)
            ->line('Name: ' . $user->first_name)
            ->line('Account No: ' . $this->payment->to)
            ->line('Deposit Amount: ' . $this->payment->amount)
            ->line('TxID: ' . $this->payment->TxID)
            ->line('Click the button to proceed with approval')
            ->action('Approval', 'https://login.qcgbrokertw.com/approval/' . $token)
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
