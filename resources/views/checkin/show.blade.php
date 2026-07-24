@extends('layouts.app')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Check In</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-md mx-auto px-4 text-center">
        @foreach(['success', 'error', 'info'] as $level)
        @if(session($level))
        <div class="mb-6 rounded-xl p-4 text-sm border
            {{ $level === 'success' ? 'bg-green-50 border-green-200 text-green-700' : ($level === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
            {{ session($level) }}
        </div>
        @endif
        @endforeach

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
            <div class="text-6xl mb-6">🏋️</div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-gray-500 text-sm mb-8">Tap the button below to check in for today's session.</p>

            @php $user = auth()->user(); $membership = $user->activeMembership(); @endphp

            @if(! $user->hasAcceptedWaiver())
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-700">
                    You must sign the membership waiver before checking in.
                    <a href="{{ route('waiver.show') }}" class="font-semibold underline ml-1">Sign now</a>
                </div>
            @elseif(! $user->canCheckIn())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
                    {{ $membership ? 'Your membership is ' . $membership->status . '. Please renew to check in.' : 'You have no active membership.' }}
                    <a href="{{ route('member.dashboard') }}" class="font-semibold underline ml-1">Go to member portal</a>
                </div>
            @else
                <form method="POST" action="{{ route('checkin.store') }}">
                    @csrf
                    <button type="submit"
                            class="w-full bg-gray-900 text-white py-4 px-8 rounded-2xl font-bold text-lg hover:bg-gray-700 active:scale-95 transition-all shadow-lg">
                        ✓ Check Me In
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-4">Or show your <a href="{{ route('member.dashboard') }}" class="underline">QR code</a> at reception.</p>
            @endif
        </div>
    </div>
</div>
