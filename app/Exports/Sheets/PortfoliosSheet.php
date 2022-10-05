<?php

namespace App\Exports\Sheets;

use App\Models\Dividend;
use App\Models\Portfolio;
use App\Models\Split;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PortfoliosSheet implements FromCollection, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Notes',
            'Wishlist',
            'Created',
            'Updated',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Portfolio::myPortfolios()->get();
    }

     /**
     * @return string
     */
    public function title(): string
    {
        return 'Portfolios';
    }
}
