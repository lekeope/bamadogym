<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'status', 'payment_method',
        'start_date', 'renewal_date', 'frozen_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'renewal_date' => 'date',
            'frozen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'due']);
    }

    public function daysUntilRenewal(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->renewal_date, false);
    }

    public function refreshStatus(): void
    {
        $days = $this->daysUntilRenewal();

        if ($this->status === 'frozen') {
            return;
        }

        if ($days > 7) {
            $this->status = 'active';
        } elseif ($days >= 0) {
            $this->status = 'due';
        } elseif ($days >= -7) {
            $this->status = 'overdue';
        } else {
            $this->status = 'expired';
        }

        $this->save();
    }

    public function extendByPlan(): void
    {
        $this->renewal_date = Carbon::parse($this->renewal_date)->addDays($this->plan->duration_days);
        $this->refreshStatus();
    }
}
