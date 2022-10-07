<?php

namespace App\Http\Livewire;

use App\Models\Holding;
use App\Models\Portfolio;
use App\Traits\FormatsMoney;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PortfolioHoldingsTable extends DataTableComponent
{
    use FormatsMoney;

    public $refresh = 1 * 60 * 1000; // poll every 1 minute

    public bool $columnSelect = true;
    public string $defaultSortColumn = 'id';
    public bool $reorderEnabled = false;
    public bool $hideBulkActionsOnEmpty = true;
    public array $bulkActions = []; 

    public $portfolio;

    public function mount(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }

    public function columns(): array
    {
        return [
            Column::make('Symbol', 'symbol')
                ->sortable()
                ->searchable()
                ->excludeFromSelectable(),
            Column::make('Symbol Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Quantity', 'quantity')
                ->sortable(),
            Column::make('Average Cost Basis', 'average_cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Total Cost Basis', 'total_cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Market Value', 'market_value')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Total Market Value', 'total_market_value')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Market Gain/Loss ($)', 'market_gain_loss_dollars')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Market Gain/Loss (%)', 'market_gain_loss_percent')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return number_format($value, 2) . "%";
                }),
            Column::make('Realized Gain/Loss ($)', 'realized_gain_loss_dollars')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Dividends Earned ($)', 'dividends_earned')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('52 week low', 'fifty_two_week_low')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('52 week high', 'fifty_two_week_high')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($value);
                }),
            Column::make('Number of Transactions', 'transactions_count')
                ->sortable(),
            Column::make('Market Data Age', 'market_data_age')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return Carbon::make($value)->diffForHumans();
                }),
            Column::make('Updated At')
                ->sortable(),
            Column::make('Created At')
                ->sortable()
        ];
    }

    public function query()
    {
        return Holding::where('portfolio_id', $this->portfolio->id)
            ->with([
                'transactions' => function ($q) {
                    $q->where('transactions.portfolio_id', $this->portfolio->id);
                }])
            ->withCount('transactions')
            ->selectRaw('((market_data.market_value - holdings.average_cost_basis) * holdings.quantity) AS market_gain_loss_dollars')
            ->selectRaw('(((market_data.market_value - holdings.average_cost_basis) / holdings.average_cost_basis) * 100) AS market_gain_loss_percent')
            ->selectRaw('market_data.market_value AS market_value')
            ->selectRaw('(holdings.quantity * market_data.market_value) AS total_market_value')
            ->selectRaw('market_data.fifty_two_week_low')
            ->selectRaw('market_data.fifty_two_week_high')
            ->selectRaw('market_data.updated_at AS market_data_age')
            ->selectRaw('market_data.name')
            ->join('market_data', 'holdings.symbol', 'market_data.symbol');
    }

    public function getTableRowUrl($row): string
    {
        return route('holding.show', ['holding' => $row->symbol, 'portfolio' => $row->portfolio_id]);
    }
}
