<x-layout>
    <x-slot:title>Edit Album - {{ $album->name }}</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('albums.show', $album) }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $album->name }}
        </a>

        <h1 class="text-3xl font-bold mt-4">Edit Album</h1>

        <form method="POST" action="{{ route('albums.update', $album) }}" enctype="multipart/form-data" data-turbo="false" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="text-sm">Album Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $album->name) }}"
                    class="input input-bordered w-full">
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="artist" class="text-sm">Artist</label>
                <input type="text" id="artist" name="artist" value="{{ old('artist', $album->artist) }}"
                    class="input input-bordered w-full" placeholder="Artist name">
            </div>

            <div>
                <label for="cover_art" class="text-sm">Cover Art</label>
                @if($album->cover_art)
                    <div class="mb-2">
                        <img src="/{{ $album->cover_art }}" alt="Current cover" class="w-24 h-24 object-cover rounded">
                    </div>
                @endif
                <input type="file" id="cover_art" name="cover_art" accept="image/*"
                    class="file-input file-input-bordered w-full">
                @error('cover_art')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Album</button>
        </form>
    </div>
</x-layout>
