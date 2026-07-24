<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaiverController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->hasAcceptedWaiver()) {
            return redirect()->route('member.dashboard');
        }

        return view('waiver.show', compact('user'));
    }

    public function accept(Request $request)
    {
        $request->validate([
            'accepted' => ['required', 'accepted'],
        ]);

        $request->user()->update(['waiver_accepted_at' => now()]);

        return redirect()->route('member.dashboard')->with('success', 'Waiver accepted. Welcome to Bamado Gym!');
    }
}
