<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Viewing Portfolio') . ': ' . $portfolio->title }}

            <x-link-button class="ml-4" href="{{ route('portfolio.index', $portfolio->id) }}">
                {{ __('Back to Home') }}
            </x-link-button>

            <x-link-button class="ml-4" href="{{ route('portfolio.edit', $portfolio->id) }}">
                {{ __('Edit Portfolio') }}
            </x-link-button>
            
            <x-link-button class="ml-4" href="{{ route('portfolio.transaction.create', $portfolio->id) }}">
                {{ __('Add Transaction') }}
            </x-link-button>
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-lg p-6 border-gray-200">
                total_dividends_earned {{ $metrics->total_dividends_earned }} <br/>
                total_gain_loss_dollars {{ $metrics->total_gain_loss_dollars }}  <br/>
                total_market_value {{ $metrics->total_market_value }}  <br/>
                total_cost_basis {{ $metrics->total_cost_basis }}  <br/>
                total_gain_loss_dollars {{ $metrics->total_gain_loss_dollars }}  <br/>
                total_gain_loss_percent  {{ $metrics->total_gain_loss_percent }}  <br/>
            </div>

            <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                @livewire('portfolio-holdings-table', ['portfolio' => $portfolio])
            </div>
        </div>
    </div>
</x-app-layout>
