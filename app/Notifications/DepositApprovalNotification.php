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

    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $user = User::find($this->transaction->user_id);
        $token = md5($user->email . $this->transaction->transaction_number);

        return (new MailMessage)
            ->subject('Deposit Approval - ' . $this->transaction->transaction_number)
            ->greeting('Deposit Approval- ' . $this->transaction->transaction_number)
            ->line('Email: ' . $user->email)
            ->line('Name: ' . $user->first_name)
            ->line('Account No: ' . $this->transaction->to_meta_login)
            ->line('Deposit Amount: ' . $this->transaction->amount)
            ->line('TxID: ' . $this->transaction->txn_hash)
            ->line('Platform: QCG Payment Channel')
            ->line('Click the button to proceed with approval')
            ->action('Approval', 'https://login.qcgexchange.com/approval/' . $token)
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
