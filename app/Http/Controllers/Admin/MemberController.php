<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member')->with('membership.plan');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('membership', fn ($q) => $q->where('status', $request->status));
        }

        $members = $query->latest()->paginate(25)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function show(User $member)
    {
        $member->load('membership.plan', 'memberships.plan', 'checkIns');
        $payments = Payment::whereHas('membership', fn ($q) => $q->where('user_id', $member->id))
            ->with('membership.plan', 'recordedBy')
            ->latest('paid_at')
            ->get();

        $plans = Plan::where('is_active', true)->get();

        return view('admin.members.show', compact('member', 'payments', 'plans'));
    }

    public function recordPayment(Request $request, User $member)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'method' => ['required', 'in:cash,transfer'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'paid_at' => ['required', 'date'],
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $membership = $member->memberships()->latest()->first();

        if ($membership && $membership->plan_id === $plan->id) {
            $membership->extendByPlan();
        } else {
            $membership = Membership::create([
                'user_id' => $member->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'payment_method' => $request->method,
                'start_date' => today(),
                'renewal_date' => today()->addDays($plan->duration_days),
            ]);
        }

        Payment::create([
            'membership_id' => $membership->id,
            'recorded_by' => $request->user()->id,
            'amount' => $plan->price,
            'method' => $request->method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'paid_at' => $request->paid_at,
        ]);

        return back()->with('success', 'Payment recorded and membership extended to ' . $membership->renewal_date->format('M j, Y') . '.');
    }

    public function updateStatus(Request $request, User $member)
    {
        $request->validate([
            'status' => ['required', 'in:active,due,overdue,expired,frozen'],
        ]);

        $membership = $member->memberships()->latest()->firstOrFail();
        $membership->update(['status' => $request->status]);

        return back()->with('success', 'Membership status updated.');
    }

    public function staffCheckIn(Request $request, User $member)
    {
        if (! $member->canCheckIn()) {
            return back()->with('error', 'Member cannot check in: membership not active or waiver not signed.');
        }

        \App\Models\CheckIn::create([
            'user_id' => $member->id,
            'method' => 'staff',
            'checked_in_by' => $request->user()->id,
            'checked_in_at' => now(),
        ]);

        return back()->with('success', $member->name . ' checked in.');
    }

    public function sendReminder(Request $request, User $member)
    {
        \App\Notifications\PaymentReminderNotification::sendToMember($member, 'manual');

        \App\Models\PaymentReminder::create([
            'user_id' => $member->id,
            'type' => 'manual',
            'channel' => 'email',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Reminder sent to ' . $member->name . '.');
    }
}
