<x-layout>
    <x-slot:title>{{ $tune->name }}</x-slot:title>

    <div class="w-full max-w-4xl mx-auto mt-8">
        <a href="#" onclick="history.back(); return false;" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>

        <h1 class="text-3xl font-bold mt-4">{{ $tune->name }}</h1>
        <p class="text-base-content/60">{{ $tune->tuneType->name }}</p>

        <div class="mt-6 space-y-6">
            @forelse($tune->settings as $setting)
                <x-setting :setting="$setting" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <p class="text-base-content/60">No settings for this tune yet.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
