{{--
    Collections Index
    =================
    Lists all collections with search and pagination.
    Clicking a collection links to its show page which displays
    the filtered tune list using x-tune-list.
--}}
<x-layout>
    <x-slot:title>Collections</x-slot:title>

    <div class="max-w-4xl mx-auto">
        {{-- Search and filter controls --}}
        <div class="flex items-center gap-4 mt-6">
            <input type="text" id="collection-filter" placeholder="Search by title..."
                class="input input-bordered w-64" />
            <select id="collection-per-page" class="select select-bordered select-sm">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>
            @auth
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" id="collection-private" class="checkbox checkbox-sm">
                    My Private Collections
                </label>
                <a href="{{ route('collections.create') }}" class="btn btn-primary btn-sm">Add Collection</a>
            @endauth
        </div>

        {{-- Collection list --}}
        <div class="mt-6 space-y-2">
            @forelse($collections as $collection)
                <a href="{{ route('collections.show', $collection) }}" class="card bg-base-100 shadow-sm block hover:shadow-md transition-shadow">
                    <div class="card-body flex-row items-center justify-between py-3">
                        <div>
                            <span class="font-semibold">{{ $collection->name }}</span>
                            @if($collection->author)
                                <span class="text-sm text-base-content/60">— {{ $collection->author }}</span>
                            @endif
                            @if($collection->description)
                                <p class="text-sm text-base-content/60 mt-1">{{ $collection->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-sm text-base-content/60">
                            <span>{{ $collection->tunes_count }} tunes</span>
                            @if($collection->is_shared)
                                <span class="badge badge-sm">Shared</span>
                            @else
                                <span class="badge badge-sm badge-ghost">Private</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-base-content/60 text-center py-8">No collections found.</p>
            @endforelse
        </div>

        {{-- Server-side pagination --}}
        <div class="mt-6">
            {{ $collections->links() }}
        </div>
    </div>
</x-layout>
