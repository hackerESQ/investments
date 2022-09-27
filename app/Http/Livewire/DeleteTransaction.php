<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Transaction;

class DeleteTransaction extends Component
{

    public Transaction $transaction;
    public $buttonText = 'x';
    public $confirmed = false;

    public function render()
    {
        return <<<'blade'
            <div>
                @if(!$confirmed)
                    <span style="cursor:pointer; float:right; font-weight: bold" wire:click="delete"> 
                        {{ $buttonText }}
                    </span>
                @else
                    <span style="cursor:pointer; float:right; font-weight: bold" wire:click="delete"> 
                        {{ $buttonText }}
                    </span>
                @endif
            </div>
        blade;
    }

    public function delete()
    {
        $this->buttonText = 'Are you sure?';

        if ($this->confirmed) {

            $transaction = $this->transaction;

            $this->transaction->delete();
            return redirect()->to(route('holding.show', [
                'portfolio' => $transaction->portfolio_id,
                'holding' => $transaction->symbol
            ]));
        }
        
        $this->confirmed = true;
    }
}
