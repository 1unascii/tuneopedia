<x-layout>
    <x-slot:title>Verify Email</x-slot:title>

    <div class="max-w-md mx-auto mt-8">
        <h1 class="text-2xl font-bold">Verify Email</h1>

        <p class="mt-4 text-sm text-base-content/70">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success mt-4">
                <span>A new verification link has been sent to the email address you provided during registration.</span>
            </div>
        @endif

        <div class="mt-6 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">Log Out</button>
            </form>
        </div>
    </div>
</x-layout>
