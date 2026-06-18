<x-layout>
    <x-slot:title>Tracks - {{ $album->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('albums.show', $album) }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $album->name }}
        </a>

        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold">Tracks — {{ $album->name }}</h1>
            <a href="{{ route('albums.tracks.create', $album) }}" class="btn btn-sm btn-primary">Add Track</a>
        </div>

        <div class="mt-6">
            @if($tracks->isEmpty())
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
                            @foreach($tracks as $track)
                                <tr>
                                    <td>{{ $track->track_number }}</td>
                                    <td>{{ $track->name }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('albums.tracks.edit', [$album, $track]) }}" class="btn btn-xs btn-ghost">Edit</a>
                                        <form method="POST" action="{{ route('albums.tracks.destroy', [$album, $track]) }}" class="inline" onsubmit="return confirm('Delete this track?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-ghost text-error">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layout>
