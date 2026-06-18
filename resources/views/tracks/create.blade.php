<x-layout>
    <x-slot:title>Add Track - {{ $album->name }}</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('albums.show', $album) }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $album->name }}
        </a>

        <h1 class="text-3xl font-bold mt-4">Add Track to {{ $album->name }}</h1>

        <form method="POST" action="{{ route('albums.tracks.store', $album) }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="track_number" class="text-sm">Track Number</label>
                <input type="number" id="track_number" name="track_number" value="{{ old('track_number') }}"
                    class="input input-bordered w-full" min="1" max="99" placeholder="1">
                @error('track_number')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="name" class="text-sm">Track Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="input input-bordered w-full" placeholder="Track name">
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Add Track</button>
        </form>
    </div>
</x-layout>
