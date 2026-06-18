<x-layout>
    <x-slot:title>Profile</x-slot:title>

    <div class="max-w-2xl mx-auto mt-8 space-y-6">
        <h1 class="text-3xl font-bold">Profile</h1>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-layout>
