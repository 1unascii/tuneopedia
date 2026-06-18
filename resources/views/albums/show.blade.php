<x-layout>
    <x-slot:title>{{ $album->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('albums.index') }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Albums
        </a>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-4">
                @if($album->cover_art)
                    <img src="/{{ $album->cover_art }}" alt="{{ $album->name }}" class="w-24 h-24 object-cover rounded">
                @endif
                <div>
                    <h1 class="text-3xl font-bold">{{ $album->name }}</h1>
                    @if($album->artist)
                        <p class="text-base-content/60">{{ $album->artist }}</p>
                    @endif
                </div>
            </div>
            @can('update', $album)
                <div class="flex gap-2">
                    <a href="{{ route('albums.edit', $album) }}" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" action="{{ route('albums.destroy', $album) }}" onsubmit="return confirm('Delete this album and all its tracks?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-error btn-outline">Delete</button>
                    </form>
                </div>
            @endcan
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl">Tracks</h2>
                @can('addTrack', $album)
                    <a href="{{ route('albums.tracks.create', $album) }}" class="btn btn-sm btn-outline">Add Track</a>
                @endcan
            </div>

            @if($album->tracks->isEmpty())
                <p class="text-base-content/60 text-center py-8">No tracks yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($album->tracks as $track)
                                <tr>
                                    <td>{{ $track->track_number }}</td>
                                    <td>
                                        <a href="{{ route('albums.tracks.show', [$album, $track]) }}" class="hover:text-primary">
                                            {{ $track->name }}
                                        </a>
                                    </td>
                                    @can('update', $album)
                                        <td class="text-right">
                                            <a href="{{ route('albums.tracks.edit', [$album, $track]) }}" class="btn btn-xs btn-ghost">Edit</a>
                                            <form method="POST" action="{{ route('albums.tracks.destroy', [$album, $track]) }}" class="inline" onsubmit="return confirm('Delete this track?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-ghost text-error">Delete</button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layout>
