<?php

namespace App\Http\Requests;

use App\Models\Holding;
use App\Rules\SymbolValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    protected $holding;

    public function prepareForValidation()
    {
        // uppercase the symbol
        $this->merge([
            'symbol' => strtoupper($this->input('symbol'))
        ]);

        // get holding -- or a dummy for rules purposes only
        $this->holding = Holding::firstOrNew([
            'portfolio_id' => $this->route('portfolio.id'),
            'symbol' => $this->input('symbol')
        ],[
            'average_cost_basis' => null,
            'quantity' => 0,
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'symbol' => ['required', new SymbolValidationRule],
            'date' => ['required', 'date_format:Y-m-d', 'before_or_equal:now'],
            'transaction_type' => ['required', 'in:SELL,BUY'],
            'quantity' => [
                'required', 
                'numeric', 
                'min:0',
                Rule::when($this->input('transaction_type') == "SELL", ['max:' . $this->holding->quantity])
            ],
            'cost_basis' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function passedValidation() 
    {
        // if sale, move cost basis to sale price 
        if ($this->input('transaction_type') == 'SELL') {

            $this->merge([
                'sale_price' => $this->input('cost_basis'),
                'cost_basis' => $this->holding->average_cost_basis ?? $this->input('cost_basis'),
            ]);
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'quantity.max' => 'Insufficient quantity. You only have ' . $this->holding->quantity . ' shares of ' . $this->input('symbol') . ' available',
            'date.before' => 'The date should be before today',
        ];
    }
}
