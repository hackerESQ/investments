<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import / Export Transactions') }}

            <x-link-button class="ml-4" href="{{ route('portfolio.index') }}">
                {{ __('Back to home') }}
            </x-link-button>
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-gray-200">
                <h1 class="title">Import</h1>

                <x-jet-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" enctype="multipart/form-data" action="{{ route('transaction.import') }}">
                    @csrf

                    <div class="mt-4">
                        <x-jet-label for="import" value="{{ __('File') }}" />
                        <x-jet-input id="import" value="{{ old('import') }}" class="block mt-1 w-full" type="file" name="import" required />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-jet-button>
                            {{ __('Import') }}
                        </x-jet-button>
                    </div>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-6 border-gray-200">

                <h1 class="title">Export</h1>

                <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" href="{{ route('transaction.export') }}">
                    {{ __('Export') }}
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
