@extends('layouts.public')

@section('title', 'Check-In Denied')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-zinc-900 border border-red-700/40 rounded-2xl p-12 text-center max-w-sm w-full">
        <div class="text-6xl mb-6">🚫</div>
        <h1 class="text-3xl font-extrabold text-white mb-2">Cannot Check In</h1>
        <p class="text-zinc-400 text-lg mb-4">Hi, <strong class="text-white">{{ $user->name }}</strong>.</p>
        <div class="bg-red-900/30 border border-red-700/50 rounded-xl p-4 text-red-300 text-sm mb-8">
            {{ $reason }}
        </div>
        <p class="text-zinc-500 text-sm">Please visit the front desk or renew online.</p>
    </div>
</div>
@endsection
