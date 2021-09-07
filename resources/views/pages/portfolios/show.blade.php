<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portfolio') . ': ' . $portfolio->title }}

            <x-link-button class="ml-4" href="{{ route('portfolio.edit', $portfolio->id) }}">
                {{ __('Edit Portfolio') }}
            </x-link-button>
            
            <x-link-button class="ml-4" href="{{ route('transaction.create', $portfolio->id) }}">
                {{ __('Add Transaction') }}
            </x-link-button>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-lg p-6 border-gray-200">
                @livewire('portfolio-holdings-table', ['portfolio' => $portfolio])
            </div>
        </div>
    </div>
</x-app-layout>
