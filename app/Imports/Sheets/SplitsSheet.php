<?php

namespace App\Imports\Sheets;

use App\Models\Dividend;
use App\Models\Portfolio;
use App\Models\Split;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SplitsSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    // use Importable;

    public function collection(Collection $dividend)
    {
        foreach ($dividend->sortBy('date') as $row) {

            Split::updateOrCreate([
                'symbol' => $row['symbol'],
                'date' => $row['date'],
            ],[
                'symbol' => $row['symbol'],
                'split_amount' => $row['split'],
                'date' => $row['date'],
            ]);
        }
    }
}
