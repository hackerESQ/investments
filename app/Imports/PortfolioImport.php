<?php

namespace App\Imports;

use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PortfolioImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public function collection(Collection $transactions)
    {
        foreach ($transactions->sortBy('date') as $row) {

            $portfolio = Portfolio::myPortfolios()
                                ->where(['id' => $row['portfolio_id']])
                                ->orWhere(['title' => $row['portfolio_id']])
                                ->firstOr(function () use ($row) {

                                    return auth()->user()->portfolios()->create([
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
