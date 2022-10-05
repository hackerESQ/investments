<?php

namespace App\Imports\Sheets;

use App\Models\DailyChange;
use App\Models\Portfolio;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DailyChangesSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    // use Importable;

    public function collection(Collection $dailyChanges)
    {
        foreach ($dailyChanges->sortBy('date') as $row) {
            if ($row['user'] != auth()->user()->id) {
                throw new Exception('Can\'t do that.');
            }

            DailyChange::updateOrCreate([
                'date' => $row['date'],
                'user_id' => $row['user'],
            ],[
                'user_id' => $row['user'],
                'date' => $row['date'],
                'total_market_value' => $row['total_market_value'],
                'total_cost_basis' => $row['total_cost_basis'],
                'total_gain_loss' => $row['total_gain_loss'],
                'total_dividends' => $row['total_dividends'],
                'realized_gains' => $row['realized_gains'],
            ]);
        }
    }
}
