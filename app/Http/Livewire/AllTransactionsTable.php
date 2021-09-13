<?php

namespace App\Http\Livewire;

use App\Models\Portfolio;
use App\Models\Transaction;
use App\Traits\FormatsMoney;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class AllTransactionsTable extends DataTableComponent
{
    use FormatsMoney;

    public $refresh = 1 * 60 * 1000; // poll every 1 minute

    public bool $columnSelect = true;
    public string $defaultSortColumn = 'date';
    public bool $reorderEnabled = false;
    public bool $hideBulkActionsOnEmpty = true;
    public array $bulkActions = []; 

    public function columns(): array
    {
        return [
            Column::make('Date', 'date')
                ->sortable()
                ->excludeFromSelectable(),
            Column::make('Symbol', 'symbol')
                ->sortable()
                ->searchable()
                ->excludeFromSelectable(),
            
            Column::make('Symbol Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Portfolio', 'title')
                ->sortable()
                ->searchable(),
            Column::make('Transaction Type', 'transaction_type')
                ->sortable(),
            Column::make('Quantity', 'quantity')
                ->sortable(),
            Column::make('Cost Basis', 'cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Sale Price', 'sale_price')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Gain/Loss ($)', 'gain_loss_dollars')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Updated At')
                ->sortable(),
            Column::make('Created At')
                ->sortable()
        ];
    }

    public function query()
    {
        return Transaction::myTransactions()
            ->select([
                'transactions.date',
                'transactions.symbol',
                'transactions.quantity',
                'transactions.cost_basis',
                'transactions.sale_price',
                'transactions.transaction_type',
                'transactions.updated_at',
                'transactions.created_at',
            ])->selectRaw('market_data.market_value AS market_value')
            ->selectRaw('(transactions.quantity * market_data.market_value) AS total_market_value')
            ->selectRaw('(CASE 
                                WHEN transactions.transaction_type = "BUY" 
                                THEN transactions.quantity * market_data.market_value 
                                ELSE transactions.quantity * transactions.sale_price 
                                END) 
                        - (transactions.quantity * transactions.cost_basis) AS gain_loss_dollars')
            ->selectRaw('market_data.updated_at AS market_data_age')
            ->selectRaw('market_data.name')
            ->selectRaw('portfolios.title AS title')
            ->selectRaw('portfolios.id AS portfolio_id')
            ->join('market_data', 'transactions.symbol', 'market_data.symbol')
            ->join('portfolios', 'transactions.portfolio_id', 'portfolios.id');
    }

    public function getTableRowUrl($row): string
    {
        return route('portfolio.show', ['portfolio' => $row->portfolio_id]);
    }
}
