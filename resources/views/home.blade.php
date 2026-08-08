@extends('layouts.public')

@section('title', $gym['app_name'] ?? 'Bamado Gym')

@section('content')

{{-- Hero --}}
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-zinc-950 via-zinc-900 to-amber-950/30"></div>
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative z-10 text-center max-w-4xl mx-auto px-4 sm:px-6">
        <div class="inline-flex items-center gap-2 bg-amber-400/10 text-amber-400 border border-amber-400/20 rounded-full px-4 py-1.5 text-sm font-medium mb-8">
            <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
            Now open 7 days a week
        </div>
        @php
            $tagline = $gym['tagline'] ?? 'Train Hard. Live Strong.';
            $parts = preg_split('/\.\s+/', $tagline, 2);
        @endphp
        <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight leading-none mb-6">
            {{ rtrim($parts[0] ?? 'Train Hard', '.') }}.<br><span class="text-amber-400">{{ isset($parts[1]) ? rtrim($parts[1], '.') . '.' : 'Live Strong.' }}</span>
        </h1>
        <p class="text-lg sm:text-xl text-zinc-300 max-w-2xl mx-auto mb-10">
            {{ $gym['hero_subtitle'] ?? "Bamado Gym is Lagos's premier open-access fitness facility." }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ gym_whatsapp_url('Hi! I want to join '.($gym['app_name'] ?? 'Bamado Gym').'.') }}"
               target="_blank" rel="noopener"
               class="w-full sm:w-auto bg-amber-400 text-zinc-950 px-8 py-4 rounded-xl font-bold text-lg hover:bg-amber-300 transition-all hover:scale-105 shadow-lg shadow-amber-400/20">
                Chat on WhatsApp
            </a>
            <a href="#pricing" class="w-full sm:w-auto border border-zinc-700 text-zinc-300 px-8 py-4 rounded-xl font-medium hover:border-zinc-500 hover:text-white transition">
                View Pricing
            </a>
        </div>
    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</section>

{{-- Features --}}
<section id="about" class="py-24 bg-zinc-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">{{ $gym['about_heading'] ?? 'Everything you need to reach your goals' }}</h2>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">{{ $gym['about_blurb'] ?? 'No excuses. No limits. Just results.' }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['💪', 'World-Class Equipment', 'Free weights, machines, cardio — everything you need for any training style.'],
                ['🕐', 'Open 7 Days', 'Early mornings to late evenings. Your schedule, your gym.'],
                ['🏅', 'Expert Trainers', 'Certified trainers available for advice, form checks, and personal programs.'],
                ['🚿', 'Clean Facilities', 'Showers, lockers, and a space kept ready for serious training.'],
                ['🔒', 'Secure Facility', 'CCTV, secure lockers, and a safe environment for all members.'],
                ['🤝', 'Great Community', 'Join members who motivate each other every single day.'],
            ] as [$icon, $title, $desc])
            <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-6 hover:border-amber-400/30 transition-all hover:-translate-y-1">
                <div class="text-3xl mb-4">{{ $icon }}</div>
                <h3 class="text-lg font-bold text-white mb-2">{{ $title }}</h3>
                <p class="text-zinc-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Pricing --}}
<section id="pricing" class="py-24 bg-zinc-950">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Simple, transparent pricing</h2>
            <p class="text-zinc-400 text-lg">Walk in or message us to start. Plans can be adjusted for your gym.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-{{ max(count($plans), 1) }} gap-8 max-w-4xl mx-auto">
            @foreach($plans as $plan)
            @php $featured = ! empty($plan['featured']); @endphp
            <div class="relative rounded-2xl p-8 flex flex-col {{ $featured ? 'bg-amber-400 text-zinc-950 scale-105 shadow-2xl shadow-amber-400/20' : 'bg-zinc-800/50 border border-zinc-700/50 text-white' }}">
                @if($featured)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-zinc-950 text-amber-400 text-xs font-bold px-4 py-1 rounded-full border border-amber-400/30 uppercase tracking-wider">
                    Best Value
                </div>
                @endif
                <div class="mb-6">
                    <h3 class="text-2xl font-extrabold mb-1">{{ $plan['name'] }}</h3>
                    <p class="{{ $featured ? 'text-zinc-700' : 'text-zinc-400' }} text-sm">{{ $plan['description'] }}</p>
                </div>
                <div class="mb-8">
                    <span class="text-4xl font-extrabold">{{ $plan['price_label'] }}</span>
                    <span class="{{ $featured ? 'text-zinc-700' : 'text-zinc-500' }} text-sm"> / {{ $plan['duration'] }}</span>
                </div>
                <ul class="space-y-3 mb-8 flex-1">
                    @foreach(['Full gym access', 'All equipment', 'Locker use', 'Trainer advice'] as $feature)
                    <li class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 {{ $featured ? 'text-zinc-800' : 'text-amber-400' }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ gym_whatsapp_url('Hi! I am interested in the '.$plan['name'].' plan at '.($gym['app_name'] ?? 'Bamado Gym').'.') }}"
                   target="_blank" rel="noopener"
                   class="block text-center py-3 px-6 rounded-xl font-bold transition {{ $featured ? 'bg-zinc-950 text-amber-400 hover:bg-zinc-800' : 'bg-amber-400 text-zinc-950 hover:bg-amber-300' }}">
                    Message to Join
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Hours & Contact --}}
<section id="contact" class="py-24 bg-zinc-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-6">Come and train with us</h2>
                <p class="text-zinc-400 text-lg mb-8">Walk in anytime we're open, or message us to ask about membership.</p>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-amber-400/10 rounded-lg flex items-center justify-center text-amber-400 flex-shrink-0">📍</div>
                        <div>
                            <div class="font-semibold text-white">Location</div>
                            <div class="text-zinc-400 text-sm">{{ $gym['contact_address'] ?? '123 Fitness Road, Lekki, Lagos' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-amber-400/10 rounded-lg flex items-center justify-center text-amber-400 flex-shrink-0">📞</div>
                        <div>
                            <div class="font-semibold text-white">Phone</div>
                            <a href="tel:{{ preg_replace('/\s+/', '', $gym['contact_phone'] ?? '') }}" class="text-zinc-400 text-sm hover:text-amber-400 transition">{{ $gym['contact_phone'] ?? '+234 800 000 0000' }}</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-amber-400/10 rounded-lg flex items-center justify-center text-amber-400 flex-shrink-0">✉️</div>
                        <div>
                            <div class="font-semibold text-white">Email</div>
                            <a href="{{ gym_mailto_url('Membership enquiry') }}" class="text-zinc-400 text-sm hover:text-amber-400 transition">{{ $gym['contact_email'] ?? 'info@bamadogym.com' }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-8">
                <h3 class="text-lg font-bold text-white mb-6">Opening Hours</h3>
                <div class="space-y-3">
                    @foreach([
                        ['Monday – Friday', $gym['hours_weekday'] ?? '5:00 AM – 10:00 PM'],
                        ['Saturday', $gym['hours_saturday'] ?? '6:00 AM – 8:00 PM'],
                        ['Sunday', $gym['hours_sunday'] ?? '8:00 AM – 4:00 PM'],
                        ['Public Holidays', $gym['hours_holiday'] ?? '8:00 AM – 2:00 PM'],
                    ] as [$day, $time])
                    <div class="flex justify-between items-center py-2 border-b border-zinc-700/50 last:border-0">
                        <span class="text-zinc-300 text-sm font-medium">{{ $day }}</span>
                        <span class="text-amber-400 text-sm font-semibold">{{ $time }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ gym_whatsapp_url('Hi! I want to visit '.($gym['app_name'] ?? 'Bamado Gym').'.') }}"
                   target="_blank" rel="noopener"
                   class="mt-6 block text-center bg-amber-400 text-zinc-950 py-3 rounded-xl font-bold hover:bg-amber-300 transition">
                    WhatsApp Us
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
