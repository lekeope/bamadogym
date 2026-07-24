@extends('layouts.public')

@section('title', 'Checked In')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-zinc-900 border border-zinc-700/50 rounded-2xl p-12 text-center max-w-sm w-full">
        <div class="text-6xl mb-6">✅</div>
        <h1 class="text-3xl font-extrabold text-white mb-2">Checked In!</h1>
        <p class="text-zinc-400 text-lg mb-6">Welcome, <strong class="text-white">{{ $user->name }}</strong>!</p>
        <p class="text-zinc-500 text-sm mb-8">Have a great workout session. 💪</p>
        <div class="text-xs text-zinc-600">{{ now()->format('D, M j · g:i A') }}</div>
    </div>
</div>
@endsection
