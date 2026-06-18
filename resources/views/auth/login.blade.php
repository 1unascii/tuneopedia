<x-layout>
    <x-slot:title>
        Log In
    </x-slot:title>

    <div class="max-w-md mx-auto mt-16">
        <h1 class="text-3xl font-bold text-center mb-8">Log In</h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="card bg-base-100">
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="label text-sm">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label for="password" class="label text-sm">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm" />
                            <span class="ms-2 text-sm">Remember me</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-primary hover:underline" href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary btn-sm ms-3">Log in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
