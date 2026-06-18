<x-layout>
    <x-slot:title>Edit Track - {{ $track->name }}</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('albums.show', $album) }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $album->name }}
        </a>

        <h1 class="text-3xl font-bold mt-4">Edit Track</h1>

        <form method="POST" action="{{ route('albums.tracks.update', [$album, $track]) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="track_number" class="text-sm">Track Number</label>
                <input type="number" id="track_number" name="track_number" value="{{ old('track_number', $track->track_number) }}"
                    class="input input-bordered w-full" min="1" max="99">
                @error('track_number')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="name" class="text-sm">Track Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $track->name) }}"
                    class="input input-bordered w-full">
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Track</button>
        </form>
    </div>
</x-layout>
