<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

#[Fillable(['name', 'email', 'phone', 'emergency_contact', 'photo', 'role', 'checkin_token', 'waiver_accepted_at', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Billable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'waiver_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class)->latestOfMany();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function paymentReminders(): HasMany
    {
        return $this->hasMany(PaymentReminder::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin']);
    }

    public function hasAcceptedWaiver(): bool
    {
        return $this->waiver_accepted_at !== null;
    }

    public function activeMembership(): ?Membership
    {
        return $this->memberships()
            ->whereIn('status', ['active', 'due'])
            ->latest()
            ->first();
    }

    public function canCheckIn(): bool
    {
        if (! $this->hasAcceptedWaiver()) {
            return false;
        }

        $membership = $this->activeMembership();

        return $membership !== null && in_array($membership->status, ['active', 'due']);
    }
}
