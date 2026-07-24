<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return view('checkin.show', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->hasAcceptedWaiver()) {
            return redirect()->route('waiver.show')->with('error', 'Please accept the membership waiver before checking in.');
        }

        if (! $user->canCheckIn()) {
            $membership = $user->memberships()->latest()->first();
            $reason = $membership
                ? 'Your membership has ' . $membership->status . '. Please renew to check in.'
                : 'You do not have an active membership.';

            return back()->with('error', $reason);
        }

        // Prevent duplicate check-in within 1 hour
        $recent = CheckIn::where('user_id', $user->id)
            ->where('checked_in_at', '>=', now()->subHour())
            ->exists();

        if ($recent) {
            return back()->with('info', 'You already checked in recently.');
        }

        CheckIn::create([
            'user_id' => $user->id,
            'method' => 'self',
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Checked in successfully! Welcome, ' . $user->name . '!');
    }

    public function token(string $token)
    {
        $user = User::where('checkin_token', $token)->firstOrFail();

        if (! $user->hasAcceptedWaiver()) {
            return redirect()->route('waiver.show')->with('error', 'Please accept the waiver first.');
        }

        if (! $user->canCheckIn()) {
            $membership = $user->memberships()->latest()->first();
            $reason = $membership
                ? 'Membership ' . $membership->status . '. Please renew.'
                : 'No active membership found.';

            return view('checkin.denied', compact('user', 'reason'));
        }

        $recent = CheckIn::where('user_id', $user->id)
            ->where('checked_in_at', '>=', now()->subHour())
            ->exists();

        if (! $recent) {
            CheckIn::create([
                'user_id' => $user->id,
                'method' => 'self',
                'checked_in_at' => now(),
            ]);
        }

        return view('checkin.success', compact('user'));
    }
}
