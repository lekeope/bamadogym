@extends('layouts.app')

@section('title', 'My Membership')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Membership</h2>
</x-slot>

@php $statusColors = ['active' => 'green', 'due' => 'yellow', 'overdue' => 'orange', 'expired' => 'red', 'frozen' => 'blue']; @endphp

<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @foreach(['success', 'error', 'info'] as $level)
        @if(session($level))
        <div class="rounded-xl p-4 text-sm border
            {{ $level === 'success' ? 'bg-green-50 border-green-200 text-green-700' : ($level === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
            {{ session($level) }}
        </div>
        @endif
        @endforeach

        {{-- Waiver warning --}}
        @if(! $user->hasAcceptedWaiver())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <span class="text-2xl">⚠️</span>
            <div>
                <p class="font-semibold text-amber-800">You haven't signed the membership waiver yet.</p>
                <p class="text-sm text-amber-700 mt-1">You must accept the waiver before you can check in.</p>
                <a href="{{ route('waiver.show') }}" class="inline-block mt-2 text-sm font-semibold text-amber-800 underline hover:no-underline">Sign Waiver Now →</a>
            </div>
        </div>
        @endif

        {{-- Membership status card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Membership Status</p>
                    @if($membership)
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-gray-900">{{ $membership->plan->name }}</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                            {{ match($membership->status) {
                                'active' => 'bg-green-100 text-green-700',
                                'due' => 'bg-yellow-100 text-yellow-700',
                                'overdue' => 'bg-orange-100 text-orange-700',
                                'expired' => 'bg-red-100 text-red-700',
                                'frozen' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ $membership->status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        @if($membership->status === 'frozen')
                            Membership frozen since {{ $membership->frozen_at?->format('M j, Y') }}.
                        @elseif($membership->daysUntilRenewal() >= 0)
                            Renews in <strong class="text-gray-900">{{ $membership->daysUntilRenewal() }} day{{ $membership->daysUntilRenewal() !== 1 ? 's' : '' }}</strong>
                            — {{ $membership->renewal_date->format('M j, Y') }}
                        @else
                            Expired {{ abs($membership->daysUntilRenewal()) }} day{{ abs($membership->daysUntilRenewal()) !== 1 ? 's' : '' }} ago.
                        @endif
                    </p>
                    @else
                    <p class="text-lg font-semibold text-gray-700">No active membership</p>
                    <p class="text-sm text-gray-500 mt-1">Choose a plan to get started.</p>
                    @endif
                </div>

                <div class="flex flex-col gap-2">
                    <a href="{{ route('checkin.show') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-700 transition text-center">
                        Check In
                    </a>
                    @if($membership && ! $membership->isActive())
                    <a href="#renew" class="bg-amber-400 text-gray-900 px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-amber-300 transition text-center">
                        Renew Now
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- QR check-in card --}}
        @if($user->checkin_token && $user->hasAcceptedWaiver())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-1">Your QR Check-In Code</h3>
            <p class="text-sm text-gray-500 mb-4">Show this QR code at the entrance or scan it yourself.</p>
            <div class="flex items-center gap-6">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('checkin.token', $user->checkin_token)) }}"
                         alt="Check-in QR Code" class="w-28 h-28" loading="lazy">
                </div>
                <div class="text-sm text-gray-500">
                    <p class="font-medium text-gray-700 mb-1">How to use:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Show this QR to staff at reception, or</li>
                        <li>Scan with your phone camera at the entrance</li>
                    </ol>
                    <p class="mt-3 text-xs text-gray-400">Your token is unique and tied to your account.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Renew section --}}
        @if(! $membership || ! $membership->isActive())
        <div id="renew" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Renew or Start Membership</h3>
            <p class="text-sm text-gray-500 mb-4">Choose a plan and pay online. Or visit the gym to pay at the desk.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach(\App\Models\Plan::where('is_active', true)->orderBy('price')->get() as $plan)
                <form method="POST" action="{{ route('stripe.checkout') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                        <div class="font-bold text-gray-900">{{ $plan->name }}</div>
                        <div class="text-amber-500 font-extrabold text-xl">{{ $plan->formattedPrice() }}</div>
                        <div class="text-gray-400 text-xs mb-3">{{ $plan->duration_days }} days</div>
                        <button type="submit" class="w-full text-sm bg-gray-900 text-white py-2 rounded-lg font-semibold hover:bg-gray-700 transition">
                            Pay Online
                        </button>
                    </div>
                </form>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent check-ins --}}
        @if($recentCheckIns->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900">Recent Visits</h3>
                <span class="text-xs text-gray-400">Last {{ $recentCheckIns->count() }} check-ins</span>
            </div>
            <div class="space-y-2">
                @foreach($recentCheckIns as $checkIn)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-700">{{ $checkIn->checked_in_at->format('D, M j') }}</span>
                    <span class="text-xs text-gray-400">{{ $checkIn->checked_in_at->format('g:i A') }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('member.payments') }}" class="block mt-4 text-center text-sm text-amber-600 font-medium hover:underline">View Payment History →</a>
        </div>
        @endif

    </div>
</div>
