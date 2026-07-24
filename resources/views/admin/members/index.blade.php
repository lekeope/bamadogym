@extends('layouts.app')

<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Members</h2>
        <span class="text-sm text-gray-500">{{ $members->total() }} total</span>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..."
                   class="flex-1 min-w-48 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
            <select name="status" class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-amber-400">
                <option value="">All Statuses</option>
                @foreach(['active', 'due', 'overdue', 'expired', 'frozen'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-700 transition">Search</button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.members.index') }}" class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:border-gray-400 transition">Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($members->isEmpty())
            <div class="p-12 text-center text-gray-400">No members found.</div>
            @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Renewal</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 text-sm">{{ $member->name }}</div>
                            <div class="text-xs text-gray-400">{{ $member->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $member->phone ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $member->membership?->plan?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php $s = $member->membership?->status; @endphp
                            @if($s)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase
                                {{ match($s) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'due' => 'bg-yellow-100 text-yellow-700',
                                    'overdue' => 'bg-orange-100 text-orange-700',
                                    'expired' => 'bg-red-100 text-red-700',
                                    'frozen' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">{{ $s }}</span>
                            @else
                            <span class="text-xs text-gray-400">No membership</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $member->membership?->renewal_date?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.members.show', $member) }}"
                               class="text-sm font-medium text-amber-600 hover:text-amber-800 transition">
                                View →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
