{{--
    Collection Create
    =================
    Form for creating a new collection by uploading ABC files or pasting ABC text.
    The server parses the ABC, finds or creates each tune, and links them
    to the new collection.
--}}
<x-layout>
    <x-slot:title>Create Collection</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('collections.index') }}" class="text-sm hover:text-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Collections
        </a>

        <h1 class="text-3xl font-bold mt-4">Create Collection</h1>

        <form method="POST" action="{{ route('collections.store') }}" enctype="multipart/form-data"
            data-turbo="false" class="mt-6 space-y-4">
            @csrf

            {{-- Collection name --}}
            <div>
                <label for="name" class="text-sm">Collection Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="input input-bordered w-full" placeholder="e.g. Session Reels" required>
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Author --}}
            <div>
                <label for="author" class="text-sm">Author</label>
                <input type="text" id="author" name="author" value="{{ old('author') }}"
                    class="input input-bordered w-full" placeholder="Optional">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="text-sm">Description</label>
                <textarea id="description" name="description" rows="2"
                    class="textarea textarea-bordered w-full"
                    placeholder="What is this collection about?">{{ old('description') }}</textarea>
            </div>

            {{-- Options --}}
            <div class="flex gap-6">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" name="is_shared" class="checkbox checkbox-sm" checked>
                    Make this collection public
                </label>
            </div>

            {{-- ABC file upload --}}
            <div>
                <label class="text-sm">Upload ABC Files</label>
                <input type="file" name="abc_files[]" multiple accept=".abc,.txt"
                    class="file-input file-input-bordered w-full">
            </div>

            {{-- ABC text paste --}}
            <div>
                <label for="abc_text" class="text-sm">Or Paste ABC Notation</label>
                <textarea id="abc_text" name="abc_text" rows="12"
                    class="textarea textarea-bordered w-full font-mono text-sm"
                    spellcheck="false"
                    placeholder="X:1&#10;T:Drowsy Maggie&#10;R:Reel&#10;M:4/4&#10;L:1/8&#10;K:Edor&#10;|:ABCD EFGA|...">{{ old('abc_text') }}</textarea>
                @error('abc_text')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Import Collection</button>
        </form>
    </div>
</x-layout>
