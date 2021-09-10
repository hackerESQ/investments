<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Portfolio') }}

            <x-link-button class="ml-4" href="{{ route('portfolio.index') }}">
                {{ __('Back to home') }}
            </x-link-button>
        </h2>
        
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-gray-200">
                <x-jet-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('portfolio.store') }}">
                    @csrf
                        
                    <div class="mt-4">
                        <x-jet-label for="title" value="{{ __('Portfolio Title') }}" />
                        <x-jet-input id="title" class="block mt-1 w-full" type="text" name="title" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="notes" value="{{ __('Notes') }}" />
                        <x-text-area id="notes" class="block mt-1 w-full" type="text" name="notes" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-jet-button>
                            {{ __('Create Portfolio') }}
                        </x-jet-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
