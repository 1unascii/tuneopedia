<x-layout>
    <x-slot:title>Add Setting - {{ $tune->name }}</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mt-8">Add Setting to {{ $tune->name }}</h1>
        <div class="mt-8">
            <x-tune-form mode="add-setting" :tune="$tune" :instruments="$instruments" :albums="$albums" />
        </div>
    </div>
</x-layout>
