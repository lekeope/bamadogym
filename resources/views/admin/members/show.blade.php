@extends('layouts.app')

<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.members.index') }}" class="text-gray-400 hover:text-gray-600 text-sm transition">← Members</a>
        <span class="text-gray-300">/</span>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $member->name }}</h2>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @foreach(['success', 'error'] as $level)
        @if(session($level))
        <div class="rounded-xl p-4 text-sm border {{ $level === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">
            {{ session($level) }}
        </div>
        @endif
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Member info --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center text-2xl font-bold text-gray-600 mb-4">
                        {{ substr($member->name, 0, 1) }}
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ $member->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                    @if($member->phone)
                    <p class="text-sm text-gray-500 mt-1">{{ $member->phone }}</p>
                    @endif
                    @if($member->emergency_contact)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Emergency Contact</p>
                        <p class="text-sm text-gray-600">{{ $member->emergency_contact }}</p>
                    </div>
                    @endif
                    <div class="mt-3 pt-3 border-t border-gray-100 flex flex-col gap-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">Waiver Signed</span>
                            <span class="{{ $member->hasAcceptedWaiver() ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                {{ $member->hasAcceptedWaiver() ? $member->waiver_accepted_at->format('M j, Y') : 'Not signed' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">Member since</span>
                            <span class="text-gray-700">{{ $member->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Quick Actions</h4>
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('admin.members.checkin', $member) }}">
                            @csrf
                            <button type="submit" class="w-full text-sm bg-gray-900 text-white py-2.5 rounded-xl font-semibold hover:bg-gray-700 transition">
                                ✓ Check In Member
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.members.reminder', $member) }}">
                            @csrf
                            <button type="submit" class="w-full text-sm border border-gray-300 text-gray-700 py-2.5 rounded-xl font-medium hover:border-amber-400 hover:text-amber-700 transition">
                                📧 Send Reminder
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Status override --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Override Status</h4>
                    <form method="POST" action="{{ route('admin.members.status', $member) }}" class="flex gap-2">
                        @csrf
                        <select name="status" class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-amber-400">
                            @foreach(['active', 'due', 'overdue', 'expired', 'frozen'] as $s)
                            <option value="{{ $s }}" {{ $member->membership?->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="text-sm bg-amber-400 text-gray-900 px-4 rounded-xl font-semibold hover:bg-amber-300 transition">Save</button>
                    </form>
                </div>
            </div>

            {{-- Main content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Current membership --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-900 mb-4">Current Membership</h4>
                    @if($member->membership)
                    @php $m = $member->membership; @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Plan</p>
                            <p class="font-semibold text-gray-900">{{ $m->plan->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase
                                {{ match($m->status) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'due' => 'bg-yellow-100 text-yellow-700',
                                    'overdue' => 'bg-orange-100 text-orange-700',
                                    'expired' => 'bg-red-100 text-red-700',
                                    'frozen' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">{{ $m->status }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Started</p>
                            <p class="font-semibold text-gray-900">{{ $m->start_date->format('M j, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Renews</p>
                            <p class="font-semibold {{ $m->daysUntilRenewal() < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $m->renewal_date->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">No membership recorded.</p>
                    @endif
                </div>

                {{-- Record payment --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-900 mb-4">Record Manual Payment</h4>
                    <form method="POST" action="{{ route('admin.members.payment', $member) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Plan</label>
                                <select name="plan_id" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                                    @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ $member->membership?->plan_id === $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} ({{ $plan->formattedPrice() }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Payment Method</label>
                                <select name="method" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Payment Date</label>
                                <input type="date" name="paid_at" value="{{ today()->toDateString() }}" required
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Reference / Receipt No.</label>
                                <input type="text" name="reference" placeholder="e.g. TXN123456"
                                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Notes (optional)</label>
                            <input type="text" name="notes"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                        </div>
                        <button type="submit" class="bg-amber-400 text-gray-900 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-amber-300 transition">
                            Record Payment & Extend Membership
                        </button>
                    </form>
                </div>

                {{-- Payment history --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h4 class="font-bold text-gray-900">Payment History</h4>
                    </div>
                    @if($payments->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-sm">No payments recorded.</div>
                    @else
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($payments as $payment)
                            <tr>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $payment->membership->plan->name }}</td>
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $payment->formattedAmount() }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="capitalize inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $payment->method }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-400">{{ $payment->recordedBy?->name ?? ($payment->method === 'stripe' ? 'Stripe' : '—') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- Recent check-ins --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-900 mb-4">Recent Check-Ins</h4>
                    @if($member->checkIns->isEmpty())
                    <p class="text-sm text-gray-400">No check-ins recorded.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($member->checkIns->take(10) as $checkIn)
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-700">{{ $checkIn->checked_in_at->format('D, M j') }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">{{ $checkIn->checked_in_at->format('g:i A') }}</span>
                                <span class="text-xs {{ $checkIn->method === 'staff' ? 'text-amber-600' : 'text-gray-400' }}">
                                    {{ $checkIn->method === 'staff' ? 'staff' : 'self' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
