<x-layout>
    <x-slot:title>{{ $setting->name }}</x-slot:title>

    <div class="w-full max-w-4xl mx-auto mt-8">
        <a href="{{ route('tunes.index') }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Tunebook
        </a>

        <div class="mt-4">
            <x-setting :setting="$setting" :showTuneInfo="true" />
        </div>

        @if($setting->tune->settings()->count() > 1)
            <div class="mt-4 text-center">
                <a href="{{ route('tunes.show', $setting->tune) }}" class="btn btn-sm btn-outline">
                    View all settings for {{ $setting->tune->name }}
                </a>
            </div>
        @endif
    </div>
</x-layout>
