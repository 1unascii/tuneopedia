<x-layout>
    <x-slot:title>{{ $discussionThread->title }}</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="/discussion-threads" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Discussions
        </a>

        <!-- Original Thread -->
        <div class="mt-4">
            <x-discussion-thread :discussionThread="$discussionThread" />
        </div>

        <!-- Replies -->
        <div class="mt-6 space-y-4">
            <h3 class="text-lg">Replies ({{ $discussionThread->discussionReplies->count() }})</h3>

            @forelse($discussionThread->discussionReplies as $reply)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body py-3">
                        <div class="flex space-x-3">
                            <div class="avatar">
                                <div class="size-8 rounded-full">
                                    <img src="https://avatars.laravel.cloud/{{ urlencode($reply->user->email ?? '') }}"
                                        alt="{{ $reply->user->name ?? 'Anonymous' }}" class="rounded-full" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex justify-between w-full">
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm font-semibold">{{ $reply->user->name ?? 'Anonymous' }}</span>
                                        <span class="text-base-content/60">·</span>
                                        <span class="text-sm text-base-content/60">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    @can('update', $reply)
                                        <div class="flex gap-1">
                                            <a href="/discussion-replies/{{ $reply->id }}/edit" class="btn btn-ghost btn-xs">Edit</a>
                                            <form method="POST" action="/discussion-replies/{{ $reply->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this reply?')"
                                                    class="btn btn-ghost btn-xs text-error">Delete</button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                                <p class="mt-1 text-sm">{{ $reply->body }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-base-content/60 text-center py-4">No replies yet. Be the first to respond!</p>
            @endforelse
        </div>

        <!-- Reply Form -->
        @auth
        <div class="mt-6">
            <form method="POST" action="/discussion-threads/{{ $discussionThread->id }}/replies">
                @csrf
                <textarea name="body" rows="3" class="textarea textarea-bordered w-full"
                    placeholder="Write a reply..."></textarea>
                @error('body')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
                <div class="mt-2 flex justify-end">
                    <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                </div>
            </form>
        </div>
        @endauth
    </div>
</x-layout>
