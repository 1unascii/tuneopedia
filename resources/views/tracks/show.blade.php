<x-layout>
    <x-slot:title>{{ $track->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('albums.show', $album) }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $album->name }}
        </a>

        <div class="mt-4">
            <h1 class="text-3xl font-bold">{{ $track->name }}</h1>
            <p class="text-base-content/60 mt-1">Track {{ $track->track_number }} on {{ $album->name }}</p>
        </div>
    </div>
</x-layout>
