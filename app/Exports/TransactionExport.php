<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            '#',
            'Symbol',
            'Portfolio',
            'Transaction',
            'Quantity',
            'Cost Basis',
            'Sale Price',
            'Date',
            'Created',
            'Updated',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaction::get();
    }
}
