{{--
    Collection Show
    ===============
    Displays a collection's tunes using the shared x-tune-list component.
    Accessible at /collections/{collection} for direct linking.
    Uses the same tune list component as the tunes index page.
--}}
<x-layout>
    <x-slot:title>{{ $collection->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('collections.index') }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Collections
        </a>
    </div>

    <x-tune-list :tuneTypes="$tuneTypes" :title="$collection->name" />
</x-layout>
