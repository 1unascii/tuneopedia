<x-layout>
    <x-slot:title>
        Register
    </x-slot:title>

    <div class="max-w-md mx-auto mt-16">
        <h1 class="text-3xl font-bold text-center mb-8">Register</h1>

        <div class="card bg-base-100">
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- User Name -->
                    <div>
                        <label for="name" class="label text-sm">User Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <label for="email" class="label text-sm">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <label for="password" class="label text-sm">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <label for="password_confirmation" class="label text-sm">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a class="text-sm text-primary hover:underline" href="{{ route('login') }}" wire:navigate>
                            Already registered?
                        </a>

                        <button type="submit" class="btn btn-primary btn-sm ms-4">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
