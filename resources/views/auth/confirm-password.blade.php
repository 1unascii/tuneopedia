<x-layout>
    <x-slot:title>Confirm Password</x-slot:title>

    <div class="max-w-md mx-auto mt-8">
        <h1 class="text-2xl font-bold">Confirm Password</h1>

        <p class="mt-4 text-sm text-base-content/70">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="password" class="text-sm">Password</label>
                <input id="password" type="password" name="password"
                    class="input input-bordered w-full" required autocomplete="current-password">
                @error('password')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Confirm</button>
        </form>
    </div>
</x-layout>
