<x-layout>
    <x-slot:title>Edit Setting - {{ $setting->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Edit Setting: {{ $setting->name }}</h1>
        <div class="mt-8">
            <x-tune-form mode="edit-setting" :tune="$tune" :setting="$setting" :instruments="$instruments" :albums="$albums" />
        </div>
    </div>
</x-layout>
