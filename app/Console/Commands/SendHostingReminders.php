<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Mail\HostingReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendHostingReminders extends Command
{
    protected $signature = 'subscriptions:send-reminders';
    protected $description = 'Send hosting renewal reminders at scheduled intervals';

    /**
     * Laravel 12+ allows this directly here.
     */
    public function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        $schedule->daily()->withoutOverlapping();
    }

    public function handle()
    {
        $today = Carbon::today();
        $milestones = [30, 14, 7, 3, 0, -7];

        foreach ($milestones as $days) {
            $date = $today->copy()->addDays($days);

            $subscriptions = Subscription::whereDate('ends_at', $date)
                ->whereNotNull('ends_at')
                ->with('user')
                ->get();

            if ($subscriptions->isEmpty()) {
                $this->info("No subscriptions found for milestone {$days} days.");
                continue;
            }

            $this->info("Sending reminders for milestone: {$days} days");

            $this->withProgressBar($subscriptions, function ($subscription) use ($days) {
                $user = $subscription->user;

                if (!$user) {
                    $this->warn("\nSkipping subscription ID {$subscription->id}: no user.");
                    return;
                }

                Mail::to($user->email)->queue(
                    new HostingReminder($user, $subscription, $days)
                );
            });

            $this->newLine(); 
            $this->info("✅ Done sending reminders for {$days} days.");
        }

        $this->info("🎉 All reminders processed successfully.");
        return 0;
    }
}
