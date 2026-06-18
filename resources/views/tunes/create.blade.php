<x-layout>
    <x-slot:title>Add Tune</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Add a Tune</h1>
        <div class="mt-8">
            <x-tune-form mode="create-tune" :tune-types="$tuneTypes" :instruments="$instruments" :albums="$albums" />
        </div>
    </div>
</x-layout>
