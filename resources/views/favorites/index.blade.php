<x-layout>
    <x-slot:title>My Favorites</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">My Favorites</h1>

        <div class="mt-6">
            @forelse($favorites as $tune)
                <div class="card bg-base-100 shadow-sm mb-2">
                    <div class="card-body flex-row items-center justify-between py-3">
                        <div class="flex items-center gap-2">
                            @if($tune->topSetting)
                                <button class="setting-preview-btn hover:text-primary"
                                        data-abc="{{ $tune->topSetting->toAbc() }}"
                                        data-setting-name="{{ $tune->topSetting->name }}">
                                    <i class="fa-solid fa-magnifying-glass-music"></i>
                                </button>
                            @else
                                <i class="fa-solid fa-magnifying-glass-music opacity-30"></i>
                            @endif
                            <a href="{{ route('tunes.show', $tune) }}" class="hover:text-primary font-semibold">
                                {{ $tune->name }}
                            </a>
                            @if($tune->tuneType)
                                <span class="text-sm text-base-content/60">— {{ $tune->tuneType->name }}</span>
                            @endif
                        </div>
                        <button class="btn btn-ghost btn-xs text-error favorite-remove"
                            data-url="{{ route('favorites.toggle', $tune) }}">
                            <i class="fa-solid fa-heart-crack"></i> Remove
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60 text-center py-8">No favorites yet. Browse the <a href="{{ route('tunes.index') }}" class="text-primary hover:underline">tunebook</a> to add some!</p>
            @endforelse
        </div>
    </div>
</x-layout>
