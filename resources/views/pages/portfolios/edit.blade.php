<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Portfolio') . ': ' . $portfolio->title }}

            <x-link-button class="ml-4" href="{{ route('portfolio.show', $portfolio->id) }}">
                {{ __('Back to portfolio') }}
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

                <form method="post" action="{{ route('portfolio.update', $portfolio->id) }}">
                    @csrf

                    <input name="_method" type="hidden" value="PUT">
                        
                    <div class="mt-4">
                        <x-jet-label for="title" value="{{ __('Portfolio Title') }}" />
                        <x-jet-input id="title" :value="$portfolio->title" class="block mt-1 w-full" type="text" name="title" required />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="notes" value="{{ __('Notes') }}" />
                        <x-text-area id="notes" :value="$portfolio->notes" class="block mt-1 w-full" type="text" name="notes" />
                    </div>

                    <div class="mt-4">
                        <x-jet-label for="users" value="{{ __('Users With Access') }}" />
                        <x-select :mandatorySelection="['pivot.owner', 1, '(owner)']"  id="users" :options="\App\Models\User::get()" :value="$portfolio->users" :multiple="true" itemText="name" class="block mt-1 w-full" name="users[]" />
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <span>@livewire('delete-portfolio', ['portfolio' => $portfolio])</span>

                        <x-jet-button>
                            {{ __('Save Changes') }}
                        </x-jet-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
