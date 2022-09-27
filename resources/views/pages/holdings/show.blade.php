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
            <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                <h1 class="text-2xl"> {{ $holding->symbol }} </h1>
                <p>{{ $holding->market_data->name }} </p>
                Quantity - {{ $holding->quantity }} <br/>
                Average Cost Basis - ${{ number_format($holding->average_cost_basis, 2) }} <br/>
                Total Cost Basis - ${{ number_format($holding->total_cost_basis, 2) }} <br/>
                Realized Gain/Loss ($) - ${{ number_format($holding->realized_gain_loss_dollars, 2) }} <br/>
                Dividends Earned - ${{ number_format($holding->dividends_earned, 2) }} <br/>
                Current Market Value - ${{ number_format($holding->market_data->market_value, 2) }} <br/>
                52 week low - ${{ number_format($holding->market_data->fifty_two_week_low, 2) }} <br/>
                52 week high - ${{ number_format($holding->market_data->fifty_two_week_high, 2) }} <br/>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Transactions</h2>
                    @foreach ($holding->transactions as $transaction)
                        <span style="float:left">{{ $transaction->date  }}</span>
                        <span>@livewire('delete-transaction', ['transaction' => $transaction])</span>
                        <br>
                    @endforeach
                </div>

                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Dividends</h2>
                    @foreach ($holding->dividends as $dividend)
                        @php
                            $owned = ($dividend->purchased - $dividend->sold);
                        @endphp 
                        {{  $dividend->date .' - $'. 
                            $dividend->dividend_amount . ' x '. 
                            $owned . ' = $'. 
                            number_format($owned * $dividend->dividend_amount, 2) }}
                        <br>
                    @endforeach
                </div>

                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Splits</h2>
                    @foreach ($holding->splits as $split)
                        {{ $split->date .' - '. $split->split_amount }}
                        <br>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
