<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Portfolio;
use Laravel\Jetstream\ConfirmsPasswords;

class DeletePortfolio extends Component
{
    use ConfirmsPasswords;

    public Portfolio $portfolio;
    public $buttonText = 'Delete Portfolio';
    public $confirmed = false;

    public function render()
    {
        return <<<'blade'
            <div>
                @if(!$confirmed)
                    <x-jet-secondary-button type="button" wire:click="delete"> 
                        {{ $buttonText }}
                    </x-jet-button>
                @else
                    <x-jet-confirms-password wire:then="delete">
                        <x-jet-secondary-button type="button"> 
                            {{ $buttonText }}
                        </x-jet-button>
                    </x-jet-confirms-password>
                @endif
            </div>
        blade;
    }

    public function delete()
    {
        $this->buttonText = 'Are you sure?';

        if ($this->confirmed) {
            $this->ensurePasswordIsConfirmed();

            $this->portfolio->delete();
            return redirect()->to('/');
        }
        
        $this->confirmed = true;
    }
}
