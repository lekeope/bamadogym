<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JoinController extends Controller
{
    public function show()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return view('join.show', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'emergency_contact' => $request->emergency_contact,
            'password' => $request->password,
            'role' => 'member',
            'checkin_token' => Str::random(32),
        ]);

        auth()->login($user);

        return redirect()->route('waiver.show');
    }
}
