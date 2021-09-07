<?php

namespace App\Traits;

use NumberFormatter;

/**
 * Undocumented trait
 */
trait FormatsMoney {

    public function formatMoney ($amount) {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($amount, 'USD');
    }

}