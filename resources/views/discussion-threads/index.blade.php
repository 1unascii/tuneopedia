<x-layout>
    <x-slot:title>
        Discussions
    </x-slot:title>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Latest Discussions</h1>

        <!-- Create Discussion Form (verified users only) -->
        @auth
        @if(auth()->user()->hasVerifiedEmail())
        <div class="card bg-base-100 shadow mt-8">
            <div class="card-body">
                <form method="POST" action="/discussion-threads">
                    @csrf
                    <div class="form-control w-full">
                        <input
                            name="title"
                            placeholder="Title"
                            class="input input-bordered w-full"
                            maxlength="255"
                            value="{{ old('title') }}"
                        >
                        @error('title')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                        <textarea
                            name="body"
                            placeholder="What's on your mind?"
                            class="textarea textarea-bordered w-full resize-none"
                            rows="4"
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Create Discussion
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endauth

        <!-- Feed -->
        <div class="space-y-4 mt-8">
            @forelse ($discussionThreads as $discussionThread)
                <x-discussion-thread :discussionThread="$discussionThread" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <div>
                            <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="mt-4 text-base-content/60">No Discussions yet. Be the first to start one!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
