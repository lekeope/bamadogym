<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bamado Gym') — Train Hard, Live Strong</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-zinc-950 text-white">

    {{-- Navigation --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-zinc-950/90 backdrop-blur border-b border-zinc-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight text-white">
                BAMADO <span class="text-amber-400">GYM</span>
            </a>
            <div class="hidden sm:flex items-center gap-6 text-sm font-medium text-zinc-300">
                <a href="{{ route('home') }}#pricing" class="hover:text-white transition">Pricing</a>
                <a href="{{ route('home') }}#about" class="hover:text-white transition">About</a>
                <a href="{{ route('home') }}#contact" class="hover:text-white transition">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('member.dashboard') }}" class="text-sm text-zinc-300 hover:text-white transition">My Account</a>
                    @if(auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm bg-amber-400 text-zinc-950 px-3 py-1.5 rounded-lg font-semibold hover:bg-amber-300 transition">Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm text-zinc-300 hover:text-white transition">Sign In</a>
                    <a href="{{ route('join.show') }}" class="text-sm bg-amber-400 text-zinc-950 px-4 py-2 rounded-lg font-semibold hover:bg-amber-300 transition">Join Now</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="pt-16">
        @yield('content')
    </div>

    <footer class="bg-zinc-900 border-t border-zinc-800 py-12 mt-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div>
                    <div class="text-lg font-extrabold text-white mb-2">BAMADO <span class="text-amber-400">GYM</span></div>
                    <p class="text-zinc-400 text-sm">Train hard. Live strong. Build a better you.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wide mb-3">Hours</h4>
                    <ul class="text-sm text-zinc-400 space-y-1">
                        <li>Mon – Fri: 5:00 AM – 10:00 PM</li>
                        <li>Saturday: 6:00 AM – 8:00 PM</li>
                        <li>Sunday: 8:00 AM – 4:00 PM</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wide mb-3">Contact</h4>
                    <ul class="text-sm text-zinc-400 space-y-1">
                        <li>📍 123 Fitness Road, Lagos</li>
                        <li>📞 +234 800 000 0000</li>
                        <li>✉️ info@bamadogym.com</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-zinc-800 text-center text-xs text-zinc-600">
                &copy; {{ date('Y') }} Bamado Gym. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
