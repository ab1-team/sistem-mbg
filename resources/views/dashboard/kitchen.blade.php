<x-app-layout title="Kitchen Dashboard">
    <x-container>
        {{-- Redirect to the specialized kitchen dashboard logic --}}
        @php
            return redirect()->route('kitchen.index');
        @endphp
    </x-container>
</x-app-layout>
