@extends('layouts.app')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment History</h2>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($payments->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <div class="text-4xl mb-4">💳</div>
                <p class="font-medium">No payments recorded yet.</p>
            </div>
            @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->membership->plan->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $payment->formattedAmount() }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="capitalize inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $payment->method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $payment->reference ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
