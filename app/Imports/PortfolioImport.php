<?php

namespace App\Imports;

use App\Models\Portfolio;
use Maatwebsite\Excel\Row;
use App\Models\Transaction;
// use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PortfolioImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;
    
    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */
    // public function model(array $row)
    // {
    //     return new Transaction([
    //         'symbol' => $row['symbol'],
    //         'portfolio_id' => Portfolio::firstOrCreate(['id' => $row['portfolio_id']], ['title' => $row['portfolio_id']])->id,
    //         'transaction_type' => $row['transaction_type'],
    //         'quantity' => $row['quantity'],
    //         'cost_basis' => $row['cost_basis'],
    //         'sale_price' => $row['sale_price'],
    //         'date' => $row['date'],
    //     ]);
    // }

    public function collection(Collection $transactions)
    {
        foreach ($transactions->sortBy('date') as $row) {
            $portfolio = Portfolio::where(['id' => $row['portfolio_id']])
                                ->orWhere(['title' => $row['portfolio_id']])
                                ->firstOr(function () use ($row) {
                                    return Portfolio::create([
                                        'title' => $row['portfolio_id']
                                    ]);
                                });

            Transaction::create([
                'symbol' => $row['symbol'],
                'portfolio_id' => $portfolio->id,
                'transaction_type' => $row['transaction_type'],
                'quantity' => $row['quantity'],
                'cost_basis' => $row['cost_basis'],
                'sale_price' => $row['sale_price'],
                'date' => $row['date'],
            ]);
        }
    }
}
