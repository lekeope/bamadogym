<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user()->load('membership.plan', 'checkIns');
        $membership = $user->membership;
        $recentCheckIns = $user->checkIns()->latest('checked_in_at')->take(10)->get();

        return view('member.dashboard', compact('user', 'membership', 'recentCheckIns'));
    }

    public function payments(Request $request)
    {
        $user = $request->user();
        $payments = \App\Models\Payment::whereHas('membership', fn ($q) => $q->where('user_id', $user->id))
            ->with('membership.plan')
            ->latest('paid_at')
            ->paginate(20);

        return view('member.payments', compact('payments'));
    }
}
