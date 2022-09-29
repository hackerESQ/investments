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
                <h1 class="text-2xl"> 
                    {{ $holding->symbol }} 
                    <span style="font-size:.65em">{{ $holding->market_data->name }}</span>
                </h1>
                <h2 style="font-weight: bold; font-size: 1.5em"> 
                    ${{ number_format($holding->market_data->market_value, 2) }} 
                </h2>
                <p> <b>Quantity Owned:</b>  {{ $holding->quantity }} </p>
                <p> <b>Average Cost Basis:</b>  ${{ number_format($holding->average_cost_basis, 2) }} </p>
                <p> <b>Total Cost Basis:</b>   ${{ number_format($holding->total_cost_basis, 2) }} </p>
                <p> <b>Realized Gain/Loss ($):</b>  ${{ number_format($holding->realized_gain_loss_dollars, 2) }} </p>
                <p> <b>Dividends Earned:</b>   ${{ number_format($holding->dividends_earned, 2) }} </p>
                <p> <b>52 week:</b>   ${{ number_format($holding->market_data->fifty_two_week_low, 2) }} - ${{ number_format($holding->market_data->fifty_two_week_high, 2) }}</p>
                <p class=" py-4" style="font-size:.75em"> Last Update: {{ $holding->market_data->updated_at }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Transactions</h2>
                    @foreach ($holding->transactions as $transaction)
                        <span style="float:left">
                            <b>{{ $transaction->date->format('Y-m-d') }}</b>
                            {{ $transaction->transaction_type }} 
                            {{ $transaction->quantity }} 
                            for 
                            ${{ number_format($transaction->sale_price ?? $transaction->cost_basis, 2) }}
                        </span>
                        <span title="delete transaction">@livewire('delete-transaction', ['transaction' => $transaction])</span>
                        <br>
                    @endforeach
                </div>

                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Dividends</h2>
                    @foreach ($holding->dividends as $dividend)
                        @php
                            $owned = ($dividend->purchased - $dividend->sold);
                        @endphp 
                        <b>{{ $dividend->date->format('Y-m-d') }}</b>
                        ${{ $dividend->dividend_amount . ' x ' }}
                        {{ $owned . ' = $' }}
                        {{ number_format($owned * $dividend->dividend_amount, 2) }}
                        <br>
                    @endforeach
                </div>

                <div class="bg-white shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">
                    <h2>Splits</h2>
                    @foreach ($holding->splits as $split)
                        <b>{{ $split->date->format('Y-m-d') }} </b>
                        {{ $split->split_amount }}:1
                        <br>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
