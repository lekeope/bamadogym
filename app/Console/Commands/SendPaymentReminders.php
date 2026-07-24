<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\PaymentReminder;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'gym:send-payment-reminders';

    protected $description = 'Send payment reminder emails to members based on membership status';

    public function handle(): void
    {
        $this->info('Refreshing membership statuses...');
        Membership::whereNotIn('status', ['frozen'])->each(fn ($m) => $m->refreshStatus());

        $rules = [
            'due_soon' => fn ($q) => $q->where('status', 'due')
                ->whereDate('renewal_date', now()->addDays(7)->toDateString()),

            'due_today' => fn ($q) => $q->where('status', 'due')
                ->whereDate('renewal_date', today()->toDateString()),

            'overdue_3' => fn ($q) => $q->where('status', 'overdue')
                ->whereDate('renewal_date', now()->subDays(3)->toDateString()),

            'overdue_7' => fn ($q) => $q->where('status', 'overdue')
                ->whereDate('renewal_date', now()->subDays(7)->toDateString()),
        ];

        foreach ($rules as $type => $scope) {
            $memberships = Membership::with('user')->tap($scope)->get();

            foreach ($memberships as $membership) {
                $user = $membership->user;

                $alreadySent = PaymentReminder::where('user_id', $user->id)
                    ->where('type', $type)
                    ->whereDate('sent_at', today())
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $user->notify(new PaymentReminderNotification($type));

                PaymentReminder::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'channel' => 'email',
                    'sent_at' => now(),
                ]);

                $this->line("Sent [{$type}] to {$user->email}");
            }
        }

        $this->info('Done.');
    }
}
