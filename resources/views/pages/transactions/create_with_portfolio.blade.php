<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Transaction') . ': ' . $portfolio->title }}

            <x-link-button class="ml-4" href="{{ route('portfolio.show', $portfolio->id) }}">
                {{ __('Back to portfolio') }}
            </x-link-button>
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-gray-200">
                <x-jet-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('portfolio.transaction.store', $portfolio->id) }}">
                    @csrf

                    <div class="mt-4">
                        <x-jet-label for="symbol" value="{{ __('Symbol') }}" />
                        <x-jet-input id="symbol" value="{{ old('symbol') ?? request('symbol') }}" class="block mt-1 w-full" type="text" name="symbol" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="transaction_type" value="{{ __('Transaction Type') }}" />
                        <x-select id="transaction_type" value="{{ old('transaction_type') }}" class="block mt-1 w-full" :blankOption="false" :options="$transaction_types" name="transaction_type" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="date" value="{{ __('Transaction Date') }}" />
                        <x-jet-input id="date" value="{{ old('date') ?? now()->toDateString() }}" class="block mt-1 w-full" type="date" name="date" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="quantity" value="{{ __('Quantity') }}" />
                        <x-jet-input id="quantity" value="{{ old('quantity') }}" class="block mt-1 w-full" type="number" name="quantity" step="any" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="cost_basis" value="{{ __('Cost Basis / Sale Price') }}" />
                        <x-jet-input id="cost_basis" value="{{ old('cost_basis') }}" class="block mt-1 w-full" type="number" name="cost_basis" step="any" required />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-jet-button>
                            {{ __('Save Transaction') }}
                        </x-jet-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
