<x-layout>
    <x-slot:title>Add Album</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('albums.index') }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Albums
        </a>

        <h1 class="text-3xl font-bold mt-4">Add Album</h1>

        <form method="POST" action="{{ route('albums.store') }}" enctype="multipart/form-data" data-turbo="false" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="text-sm">Album Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="input input-bordered w-full" placeholder="Album name">
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="artist" class="text-sm">Artist</label>
                <input type="text" id="artist" name="artist" value="{{ old('artist') }}"
                    class="input input-bordered w-full" placeholder="Artist name">
            </div>

            <div>
                <label for="cover_art" class="text-sm">Cover Art</label>
                <input type="file" id="cover_art" name="cover_art" accept="image/*"
                    class="file-input file-input-bordered w-full">
                @error('cover_art')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tracks --}}
            <div x-data="{ tracks: [{ number: 1, name: '' }] }">
                <label class="text-sm">Tracks</label>
                <div class="space-y-2 mt-1">
                    <template x-for="(track, index) in tracks" :key="index">
                        <div class="flex gap-2 items-center">
                            <input type="number" :name="'tracks[' + index + '][track_number]'" x-model="track.number"
                                class="input input-bordered w-16" min="1" max="99">
                            <input type="text" :name="'tracks[' + index + '][name]'" x-model="track.name"
                                class="input input-bordered flex-1" placeholder="Track name">
                            <button type="button" x-show="tracks.length > 1" @click="tracks.splice(index, 1)"
                                class="btn btn-ghost btn-xs text-error">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="tracks.push({ number: tracks.length + 1, name: '' })"
                    class="btn btn-ghost btn-xs mt-2">
                    <i class="fa-solid fa-plus"></i> Add Track
                </button>
            </div>

            <button type="submit" class="btn btn-primary">Create Album</button>
        </form>
    </div>
</x-layout>
