<x-layout>
    <x-slot:title>Reset Password</x-slot:title>

    <div class="max-w-md mx-auto mt-8">
        <h1 class="text-2xl font-bold">Reset Password</h1>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="text-sm">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                    class="input input-bordered w-full" required autofocus autocomplete="username">
                @error('email')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm">Password</label>
                <input id="password" type="password" name="password"
                    class="input input-bordered w-full" required autocomplete="new-password">
                @error('password')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="input input-bordered w-full" required autocomplete="new-password">
                @error('password_confirmation')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</x-layout>
