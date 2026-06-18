<x-layout>
    <x-slot:title>Forgot Password</x-slot:title>

    <div class="max-w-md mx-auto mt-8">
        <h1 class="text-2xl font-bold">Forgot Password</h1>

        <p class="mt-4 text-sm text-base-content/70">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>

        @if (session('status'))
            <div class="alert alert-success mt-4">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="text-sm">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="input input-bordered w-full" required autofocus>
                @error('email')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Email Password Reset Link</button>
        </form>
    </div>
</x-layout>
