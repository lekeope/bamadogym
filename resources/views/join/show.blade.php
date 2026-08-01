@extends('layouts.public')

@section('title', 'Join '.($gym['app_name'] ?? 'Bamado Gym'))

@section('content')
<div class="min-h-screen py-20 flex items-start justify-center">
    <div class="w-full max-w-2xl mx-auto px-4">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-white mb-3">Join {{ $gym['app_name'] ?? 'Bamado Gym' }}</h1>
            <p class="text-zinc-400">Create your account and choose a membership plan.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-900/30 border border-red-700/50 rounded-xl p-4 text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('join.store') }}" class="bg-zinc-900 border border-zinc-700/50 rounded-2xl p-8 space-y-6">
            @csrf

            {{-- Personal Info --}}
            <div>
                <h2 class="text-lg font-bold text-white mb-4 pb-2 border-b border-zinc-700">Your Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Phone Number *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Password *</label>
                        <input type="password" name="password" required
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-zinc-300 mb-1.5">Emergency Contact <span class="text-zinc-500">(optional)</span></label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="Name and phone number"
                               class="w-full bg-zinc-800 border border-zinc-600 rounded-lg px-4 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition text-sm">
                    </div>
                </div>
            </div>

            {{-- Plan selection --}}
            <div>
                <h2 class="text-lg font-bold text-white mb-4 pb-2 border-b border-zinc-700">Choose a Plan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                    <label class="cursor-pointer">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}"
                               {{ (old('plan_id', request('plan')) == $plan->id) ? 'checked' : '' }}
                               class="sr-only peer" required>
                        <div class="border-2 border-zinc-700 rounded-xl p-4 peer-checked:border-amber-400 peer-checked:bg-amber-400/5 transition-all">
                            <div class="font-bold text-white text-sm mb-1">{{ $plan->name }}</div>
                            <div class="text-amber-400 font-extrabold text-xl">{{ $plan->formattedPrice() }}</div>
                            <div class="text-zinc-500 text-xs mt-1">{{ $plan->duration_days }} days</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                <p class="text-zinc-500 text-xs mt-3">You can pay online (card) or in cash at the gym. You'll be guided to payment after signing the membership waiver.</p>
            </div>

            <button type="submit"
                    class="w-full bg-amber-400 text-zinc-950 py-3 rounded-xl font-bold text-lg hover:bg-amber-300 transition">
                Create My Account
            </button>

            <p class="text-center text-zinc-500 text-sm">
                Already a member? <a href="{{ route('login') }}" class="text-amber-400 hover:underline">Sign in</a>
            </p>
        </form>
    </div>
</div>
@endsection
