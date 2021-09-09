<?php

namespace App\Rules;

use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Contracts\Validation\Rule;

class SymbolValidationRule implements Rule
{
    public $symbol;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $symbol)
    {
        $this->symbol = $symbol;

        return app(MarketDataInterface::class)->exists($symbol);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The symbol provided (' . $this->symbol . ') is not a valid symbol';
    }
}
