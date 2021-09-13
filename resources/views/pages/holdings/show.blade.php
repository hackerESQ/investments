<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Viewing Holding') . ': ' . $holding->symbol . ' in ' . $portfolio->title }}

            <x-link-button class="ml-4" href="{{ route('portfolio.show', $portfolio->id) }}">
                {{ __('Back to Portfolio') }}
            </x-link-button>

            <x-link-button class="ml-4" href="{{ route('portfolio.transaction.create', ['portfolio' => $portfolio->id, 'symbol' => $holding->symbol]) }}">
                {{ __('Add Transaction') }}
            </x-link-button>
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-lg p-6 border-gray-200">
                @foreach ($holding->transactions as $transaction)
                    {{ $transaction->date  }}
                    <br>
                @endforeach
            </div>

            <div class="bg-white shadow-xl sm:rounded-lg mt-6 p-6 border-gray-200">
                @foreach ($holding->market_data->toArray() as $key => $value)
                    {{ $key . ' - ' . $value }}
                    <br>
                @endforeach
            </div>

            <div class="bg-white shadow-xl sm:rounded-lg mt-6 p-6 border-gray-200">
                @foreach ($holding->dividends as $dividend)
                    {{ $dividend->date .' - '. $dividend->total_received }}
                    <br>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
