<x-layout>
    <x-slot:title>Edit Reply</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="/discussion-threads/{{ $reply->discussion_thread_id }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Discussion
        </a>

        <h1 class="text-2xl font-bold mt-4">Edit Reply</h1>

        <form method="POST" action="/discussion-replies/{{ $reply->id }}" class="mt-6">
            @csrf
            @method('PUT')
            <textarea name="body" rows="4" class="textarea textarea-bordered w-full">{{ old('body', $reply->body) }}</textarea>
            @error('body')
                <span class="text-error text-sm">{{ $message }}</span>
            @enderror
            <div class="mt-2 flex justify-end">
                <button type="submit" class="btn btn-primary btn-sm">Update Reply</button>
            </div>
        </form>
    </div>
</x-layout>
