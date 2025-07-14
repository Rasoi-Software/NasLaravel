<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HostingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $days;
    public $planName;

    public function __construct($user, $subscription, $days)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->days = $days;
        $this->planName = 'Hosting Plan';
    }

    public function build()
    {
        $subject = match (true) {
            $this->days == 30 => 'Your Hosting Renewal Is Due in 30 Days',
            $this->days == 14 => 'Your Hosting Renewal Is Due in 14 Days',
            $this->days == 7  => 'IMPORTANT: Your Hosting Renewal Is Due in 7 Days',
            $this->days == 3  => 'ACTION REQUIRED: Your Hosting Renewal Is Due in 3 Days',
            $this->days == 0  => 'Action Required: Your Hosting Renewal Is Due Today',
            $this->days == -7 => 'Your Hosting Plan Has Expired – 7 Days Until Deletion',
            default            => 'Hosting Reminder',
        };
         return $this->subject($subject)
                ->view('emails.hosting_reminder', [
                    'user' => $this->user,
                    'subscription' => $this->subscription,
                    'days' => $this->days,
                    'planName' => $this->planName,
                ]);

        
    }
}
