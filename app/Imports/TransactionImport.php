<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionImport implements ToModel, WithHeadingRow, SkipsEmptyRows//, OnEachRow
{
    use Importable;
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Transaction([
            'symbol' => $row['symbol'],
            'portfolio_id' => $row['portfolio_id'],
            'transaction_type' => $row['transaction_type'],
            'quantity' => $row['quantity'],
            'cost_basis' => $row['cost_basis'],
            'sale_price' => $row['sale_price'],
            'date' => $row['date'],
        ]);
    }

    // public function onRow(Row $row)
    // {
    //     $rowIndex = $row->getIndex();
    //     $row      = $row->toArray();
    
    //     Transaction::create([
    //         'symbol' => $row['symbol'],
    //         'portfolio_id' => $row['portfolio_id'],
    //         'transaction_type' => $row['transaction_type'],
    //         'quantity' => $row['quantity'],
    //         'cost_basis' => $row['cost_basis'],
    //         'sale_price' => $row['sale_price'],
    //         'date' => $row['date'],
    //     ]);
    // }
}
