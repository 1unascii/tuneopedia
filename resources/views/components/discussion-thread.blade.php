@props(['discussionThread'])

<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex space-x-3">
            @if ($discussionThread->user)
                <div class="avatar">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/{{ urlencode($discussionThread->user->email) }}"
                            alt="{{ $discussionThread->user->name }}'s avatar" class="rounded-full" />
                    </div>
                </div>
            @else
                <div class="avatar placeholder">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/f61123d5-0b27-434c-a4ae-c653c7fc9ed6?vibe=stealth"
                            alt="Anonymous User" class="rounded-full" />
                    </div>
                </div>
            @endif

            <div class="min-w-0 flex-1">

                <!-- Discussion Thread -->
                <div class="flex justify-between w-full">
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-semibold">{{ $discussionThread->user ? $discussionThread->user->name : 'Anonymous' }}</span>
                        <span class="text-base-content/60">·</span>
                        <span class="text-sm text-base-content/60">{{ $discussionThread->created_at->diffForHumans() }}</span>
                        @if ($discussionThread->updated_at->gt($discussionThread->created_at->addSeconds(5)))
                            <span class="text-base-content/60">·</span>
                            <span class="text-sm text-base-content/60 italic">edited</span>
                        @endif
                    </div>
                    @can('update', $discussionThread)
                        <div class="flex gap-1">
                            <a href="/discussion-threads/{{ $discussionThread->id }}/edit" class="btn btn-ghost btn-xs"> Edit </a>
                            <form method="POST" action="/discussion-threads/{{ $discussionThread->id }}"> 
                                @csrf 
                                @method('DELETE') 
                                <button
                                    type="submit" onclick="return confirm('Are you sure you want to delete this discussion thread?')"
                                    class="btn btn-ghost btn-xs text-error"> 
                                    Delete 
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
                <h2 class="mt-1 font-bold">
                    <a href="/discussion-threads/{{ $discussionThread->id }}" class="hover:text-primary">{{ $discussionThread->title }}</a>
                </h2>
                <p class="mt-1">{{ $discussionThread->body }}</p>
            </div>
        </div>
    </div>
</div>