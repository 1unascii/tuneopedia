<x-layout>
    <x-slot:title>Albums</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mt-8">
            <h1 class="text-3xl font-bold">Albums</h1>
            <a href="{{ route('albums.create') }}" class="btn btn-primary btn-sm">Add Album</a>
        </div>

        <div class="mt-6 space-y-2">
            @forelse($albums as $album)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body flex-row items-center gap-3 py-3">
                        @if($album->cover_art)
                            <img src="/{{ $album->cover_art }}" alt="{{ $album->name }}" class="w-10 h-10 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <a href="{{ route('albums.show', $album) }}" class="hover:text-primary font-semibold">
                                {{ $album->name }}
                            </a>
                            @if($album->artist)
                                <span class="text-sm text-base-content/60">— {{ $album->artist }}</span>
                            @endif
                        </div>
                        <span class="text-sm text-base-content/60">{{ $album->tracks_count }} tracks</span>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60 text-center py-8">No albums yet.</p>
            @endforelse
        </div>
    </div>
</x-layout>
