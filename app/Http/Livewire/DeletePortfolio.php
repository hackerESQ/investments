<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Portfolio;

class DeletePortfolio extends Component
{
    public Portfolio $portfolio;
    public $buttonText = 'Delete Portfolio';
    public $confirmed = false;

    public function render()
    {
        return <<<'blade'
            <div><x-jet-secondary-button type="button" wire:click="delete"> {{ $buttonText }}</x-jet-button></div>
        blade;
    }

    public function delete()
    {
        $this->buttonText = 'Are you sure?';

        if ($this->confirmed) {
            $this->portfolio->delete();
            return redirect()->to('/');
        }
        
        $this->confirmed = true;
    }
}
