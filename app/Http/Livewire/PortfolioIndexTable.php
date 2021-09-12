<?php

namespace App\Http\Livewire;

use App\Models\Portfolio;
use App\Traits\FormatsMoney;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PortfolioIndexTable extends DataTableComponent
{
    use FormatsMoney;
    
    public bool $columnSelect = true;
    public string $defaultSortColumn = 'id';
    public bool $reorderEnabled = false;
    public bool $hideBulkActionsOnEmpty = true;

    public function columns(): array
    {
        return [
            Column::make('Title')
                    ->searchable()
                    ->sortable()
                    ->excludeFromSelectable(),
            Column::make('Notes')
                    ->searchable()
                    ->sortable(),          
            Column::make('Total Cost Basis', 'holdings_sum_total_cost_basis')
                    ->format(function ($value, $column, $row) {
                        return $this->formatMoney($value);
                    })->sortable(),
            Column::make('Total Market Value', 'total_market_value')
                    ->format(function ($value, $column, $row) {
                        return $this->formatMoney($value);
                    })->sortable(),
            Column::make('Market Gain / Loss ($)', 'total_gain_loss_dollars')
                    ->format(function ($value, $column, $row) {
                        return $this->formatMoney($value);
                    })->sortable(),
            Column::make('Market Gain / Loss (%)', 'total_gain_loss_percent')
                    ->format(function ($value, $column, $row) {
                        return number_format($value, 2) . "%";
                    })->sortable(),
            Column::make('Realized Gain / Loss ($)', 'holdings_sum_realized_gain_loss_dollars')
                    ->format(function ($value, $column, $row) {
                            return $this->formatMoney($value);
                    })->sortable(),
            Column::make('Dividends Earned', 'holdings_sum_dividends_earned')
                    ->format(function ($value, $column, $row) {
                            return $this->formatMoney($value);
                    })->sortable(),
            Column::make('Updated At')
                    ->sortable(),
            Column::make('Created At')
                    ->sortable(),
        ];
    }

    public function query()
    {
            return Portfolio::query()->myPortfolios()
                ->withSum('holdings', 'total_cost_basis')
                ->withSum('holdings', 'dividends_earned')
                ->withSum('holdings', 'realized_gain_loss_dollars')
                ->selectRaw('@total_market_value:=(SELECT SUM(holdings.quantity * market_data.market_value) FROM holdings JOIN market_data ON market_data.symbol = holdings.symbol WHERE portfolios.id = holdings.portfolio_id) AS total_market_value')
                ->selectRaw('@sum_total_cost_basis:=(SELECT SUM(holdings.total_cost_basis) FROM holdings WHERE portfolios.id = holdings.portfolio_id) AS sum_total_cost_basis')
                ->selectRaw('@total_gain_loss_dollars:=(@total_market_value - @sum_total_cost_basis) AS total_gain_loss_dollars')
                ->selectRaw('(@total_gain_loss_dollars / @sum_total_cost_basis) * 100 AS total_gain_loss_percent');
    }

    public function getTableRowUrl($row): string
    {
        return route('portfolio.show', $row->id);
    }
}