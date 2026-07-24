@extends('layouts.public')

@section('title', 'Membership Waiver')

@section('content')
<div class="min-h-screen py-20 flex items-start justify-center">
    <div class="w-full max-w-2xl mx-auto px-4">
        <div class="text-center mb-10">
            <div class="text-4xl mb-4">📋</div>
            <h1 class="text-3xl font-extrabold text-white mb-3">Membership Waiver</h1>
            <p class="text-zinc-400">Please read and accept the waiver to activate your membership.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-900/30 border border-red-700/50 rounded-xl p-4 text-red-300 text-sm">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-zinc-900 border border-zinc-700/50 rounded-2xl p-8">
            <div class="prose prose-invert prose-sm max-w-none h-64 overflow-y-auto rounded-xl bg-zinc-800/50 p-6 mb-6 text-zinc-300 text-sm leading-relaxed border border-zinc-700/30">
                <h3 class="text-white font-bold text-base mb-3">Bamado Gym — Membership Agreement & Liability Waiver</h3>
                <p>By accepting this waiver, I (the undersigned member) acknowledge and agree to the following:</p>
                <p><strong class="text-white">1. Risk Acknowledgement:</strong> I understand that physical exercise involves inherent risks, including the possibility of serious injury or death. I voluntarily assume all risks associated with my participation in gym activities at Bamado Gym.</p>
                <p><strong class="text-white">2. Medical Fitness:</strong> I confirm that I am physically fit to engage in physical exercise and have no medical condition that would make it unsafe for me to participate. I agree to consult a physician before beginning any new exercise program if I have any medical concerns.</p>
                <p><strong class="text-white">3. Release of Liability:</strong> I release, waive, and discharge Bamado Gym, its owners, employees, agents, and affiliates from any and all claims, demands, or causes of action arising from my use of the gym facilities, including claims arising from the negligence of Bamado Gym or its staff.</p>
                <p><strong class="text-white">4. Facility Rules:</strong> I agree to comply with all gym rules and regulations, including proper equipment use, hygiene standards, and respectful conduct toward other members and staff.</p>
                <p><strong class="text-white">5. Membership Terms:</strong> I understand that membership fees are non-refundable. I agree to renew my membership before the expiry date to maintain uninterrupted access. Bamado Gym reserves the right to revoke membership for misconduct without refund.</p>
                <p><strong class="text-white">6. Data & Privacy:</strong> I consent to Bamado Gym storing my personal information for the purpose of managing my membership, including sending renewal reminders via email. My data will not be shared with third parties without my consent.</p>
                <p><strong class="text-white">7. Emergency Contact:</strong> In the event of a medical emergency, I authorise Bamado Gym staff to contact my listed emergency contact and seek appropriate medical assistance on my behalf.</p>
                <p>This waiver is governed by Nigerian law. Any disputes shall be resolved in the courts of Lagos State.</p>
            </div>

            <form method="POST" action="{{ route('waiver.accept') }}">
                @csrf
                @error('accepted')
                <p class="text-red-400 text-sm mb-3">{{ $message }}</p>
                @enderror
                <label class="flex items-start gap-3 cursor-pointer mb-6">
                    <input type="checkbox" name="accepted" value="1" required
                           class="mt-0.5 h-4 w-4 rounded border-zinc-600 bg-zinc-800 text-amber-400 focus:ring-amber-400 focus:ring-offset-zinc-900">
                    <span class="text-sm text-zinc-300">
                        I, <strong class="text-white">{{ auth()->user()->name }}</strong>, have read and agree to the terms of this Membership Agreement & Liability Waiver. I understand the risks involved and voluntarily accept them.
                    </span>
                </label>
                <button type="submit"
                        class="w-full bg-amber-400 text-zinc-950 py-3 rounded-xl font-bold text-lg hover:bg-amber-300 transition">
                    I Accept — Activate My Membership
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
