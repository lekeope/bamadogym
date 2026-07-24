@extends('layouts.app')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
</x-slot>

<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['Total Members', $totalMembers, 'text-gray-900', '👥'],
                ['Active', $activeCount, 'text-green-600', '✅'],
                ['Due Soon', $dueCount, 'text-yellow-600', '⚠️'],
                ['Overdue / Expired', $overdueCount, 'text-red-600', '🔴'],
            ] as [$label, $value, $color, $icon])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="text-2xl mb-1">{{ $icon }}</div>
                <div class="text-2xl font-extrabold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-gray-500 font-medium">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- Today's check-ins --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Today's Check-Ins <span class="text-gray-400 font-normal">({{ today()->format('M j') }})</span></h3>
                <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $todayCheckIns->count() }} total</span>
            </div>
            @if($todayCheckIns->isEmpty())
            <div class="p-10 text-center text-gray-400 text-sm">No check-ins today yet.</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($todayCheckIns as $checkIn)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold text-sm">
                            {{ substr($checkIn->user->name, 0, 1) }}
                        </div>
                        <div>
                            <a href="{{ route('admin.members.show', $checkIn->user) }}" class="text-sm font-semibold text-gray-900 hover:text-amber-600 transition">
                                {{ $checkIn->user->name }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $checkIn->method === 'staff' ? 'Staff check-in' : 'Self check-in' }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $checkIn->checked_in_at->format('g:i A') }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('admin.members.index') }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-amber-300 transition group">
                <div class="text-2xl mb-2">👥</div>
                <div class="font-bold text-gray-900 group-hover:text-amber-600 transition">Member Management</div>
                <div class="text-sm text-gray-500 mt-1">Search, view, and manage all members.</div>
            </a>
            <a href="{{ route('admin.members.index') }}?status=overdue" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-red-300 transition group">
                <div class="text-2xl mb-2">💳</div>
                <div class="font-bold text-gray-900 group-hover:text-red-600 transition">Overdue Members</div>
                <div class="text-sm text-gray-500 mt-1">{{ $overdueCount }} member{{ $overdueCount !== 1 ? 's' : '' }} with overdue payments.</div>
            </a>
        </div>

    </div>
</div>
