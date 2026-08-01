<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gym Settings</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">← Dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="rounded-xl p-4 text-sm border bg-green-50 border-green-200 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                These settings live in the database. Keep secrets and infrastructure in Coolify / <code class="text-xs bg-amber-100 px-1 rounded">.env</code> only:
                <span class="font-medium">APP_KEY, APP_URL, DB_*, STRIPE_*, MAIL_MAILER / HOST / USERNAME / PASSWORD, SESSION_DRIVER, CACHE_STORE</span>.
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                @foreach($schema as $group)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">{{ $group['label'] }}</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        @foreach($group['fields'] as $key => $field)
                        <div>
                            <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1.5">{{ $field['label'] }}</label>
                            @if(($field['type'] ?? 'text') === 'textarea')
                            <textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $field['rows'] ?? 3 }}"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-amber-400 @error($key) border-red-400 @enderror">{{ old($key, $settings[$key] ?? '') }}</textarea>
                            @else
                            <input id="{{ $key }}" type="{{ $field['type'] ?? 'text' }}" name="{{ $key }}"
                                value="{{ old($key, $settings[$key] ?? '') }}"
                                @if(($field['type'] ?? '') === 'number') min="1" max="90" @endif
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-amber-400 @error($key) border-red-400 @enderror">
                            @endif
                            @if(!empty($field['help']))
                            <p class="mt-1 text-xs text-gray-400">{{ $field['help'] }}</p>
                            @endif
                            @error($key)
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="bg-amber-400 text-gray-900 px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-amber-300 transition">
                        Save settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
