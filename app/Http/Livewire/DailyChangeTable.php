<?php

namespace App\Http\Livewire;

use App\Models\DailyChange;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Traits\FormatsMoney;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class DailyChangeTable extends DataTableComponent
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
            Column::make('Total Market Value', 'total_market_value')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Total Cost Basis', 'total_cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Total Gain Loss', 'total_gain_loss')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Total Dividends', 'total_dividends')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Realized Gains', 'realized_gains')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Notes', 'notes')
        ];
    }

    public function query()
    {
        return DailyChange::myDailyChanges()
            ->select([
                'date',
                'total_market_value',
                'total_cost_basis',
                'total_gain_loss',
                'total_dividends',
                'realized_gains',
                'notes',
            ]);
    }
}
