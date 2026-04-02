<x-app-layout title="Profil Saya">
    <x-page-header title="Profil Saya" subtitle="Kelola informasi akun dan keamanan Anda." />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Profile Information Card --}}
        <x-card :padding="true">
            @include('profile.partials.update-profile-information-form')
        </x-card>

        {{-- Update Password Card --}}
        <x-card :padding="true">
            @include('profile.partials.update-password-form')
        </x-card>

        {{-- Delete Account Card (Full Width) --}}
        <x-card class="col-span-1 md:col-span-2" :padding="true">
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
</x-app-layout>
