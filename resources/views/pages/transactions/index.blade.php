<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions') }}

            {{-- <x-link-button class="ml-4" href="{{ route('portfolio.index') }}">
                {{ __('Back to Home') }}
            </x-link-button> --}}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-lg p-6 border-gray-200">
                @livewire('all-transactions-table')
            </div>
        </div>
    </div>
</x-app-layout>
