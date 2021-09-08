<?php

namespace App\Http\Livewire;

use App\Models\Holding;
use App\Models\Portfolio;
use App\Models\MarketData;
use App\Traits\FormatsMoney;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
// use Rappasoft\LaravelLivewireTables\Views\Filter;

class PortfolioHoldingsTable extends DataTableComponent
{
    use FormatsMoney;

    public $refresh = 1 * 60 * 1000; // poll every 1 minute
    public bool $columnSelect = true;
    public string $defaultSortColumn = 'id';
    public bool $reorderEnabled = false;
    public bool $hideBulkActionsOnEmpty = true;
    public array $bulkActions = [
        // 'delete'   => 'Delete',
    ]; 

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
            Column::make('Symbol Name', 'symbol_name')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('name')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhere(MarketData::select('name')->whereColumn('market_data.symbol', 'holdings.symbol'), 'like', '%' . $searchTerm . '%');
                })
                ->format(function ($value, $column, $row) {
                    return $row->market_data->name;
                }),
            Column::make('Quantity', 'quantity')
                ->sortable()
                ->searchable(),
            Column::make('Average Cost Basis', 'average_cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->average_cost_basis);
                }),
            Column::make('Total Cost Basis', 'total_cost_basis')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->total_cost_basis);
                }),
            Column::make('Market Value', 'market_value')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('market_value')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->market_data->market_value);
                }),
            Column::make('Total Market Value', 'market_value')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('market_value')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->market_data->market_value * $row->quantity);
                }),
            Column::make('Market Gain/Loss ($)', 'market_gain_loss_dollars')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::selectRaw('((market_data.market_value - holdings.average_cost_basis) * holdings.quantity) AS market_gain_loss_dollars')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney(($row->market_data->market_value * $row->quantity) - $row->total_cost_basis);
                }),
            Column::make('Market Gain/Loss (%)', 'market_gain_loss_percent')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::selectRaw('((market_data.market_value - holdings.average_cost_basis) / holdings.average_cost_basis) AS market_gain_loss_percent')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $row->average_cost_basis == 0 
                                ? 0 . "%"
                                : number_format((($row->market_data->market_value - $row->average_cost_basis) / $row->average_cost_basis) * 100, 2) . "%";
                }),
            Column::make('Realized Gain/Loss ($)', 'realized_gain_loss_dollars')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->realized_gain_loss_dollars);
                }),
            Column::make('Dividends Earned ($)', 'dividends_earned')
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->dividends_earned);
                }),
            Column::make('52 week low', 'fifty_two_week_low')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('fifty_two_week_low')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->market_data->fifty_two_week_low);
                }),
            Column::make('52 week high', 'fifty_two_week_high')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('fifty_two_week_high')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction);
                })
                ->format(function ($value, $column, $row) {
                    return $this->formatMoney($row->market_data->fifty_two_week_high);
                }),
            Column::make('Number of Transactions', 'transactions_count')
                ->sortable(),
            Column::make('Market Data Age', 'market_data_age')
                ->sortable(function (Builder $query, $direction) {
                    return $query->orderBy(MarketData::select('updated_at')->whereColumn('market_data.symbol', 'holdings.symbol'), $direction == 'asc' ? 'desc' : 'asc');
                })
                ->format(function ($value, $column, $row) {
                    return $row->market_data->updated_at->diffForHumans();
                }),
            Column::make('Updated At')
                ->sortable(),
            Column::make('Created At')
                ->sortable()
        ];
    }

    public function filters(): array
    {
        return [
            // 'verified' => Filter::make('E-mail Verified')
            //     ->select([
            //         ''    => 'Any',
            //         'yes' => 'Yes',
            //         'no'  => 'No',
            //     ]),
            // 'active'   => Filter::make('Active')
            //     ->select([
            //         ''    => 'Any',
            //         'yes' => 'Yes',
            //         'no'  => 'No',
            //     ]),
            // 'verified_from' => Filter::make('Verified From')
            //     ->date(),
            // 'verified_to' => Filter::make('Verified To')
            //     ->date(),
        ];
    }

    public function query()
    {
        return Holding::where('portfolio_id', $this->portfolio->id)
            ->with([
                'market_data',
                'transactions' => function ($q) {
                    $q->where('transactions.portfolio_id', $this->portfolio->id);
                }])
            ->withCount('transactions');
        //    ->when($this->getFilter('verified'), function ($query, $verified) {
        //        if ($verified === 'yes') {
        //            return $query->whereNotNull('verified');
        //        }

        //        return $query->whereNull('verified');
        //    })
        //     ->when($this->getFilter('active'), fn($query, $active) => $query->where('active', $active === 'yes'))
        //     ->when($this->getFilter('verified_from'), fn($query, $date) => $query->where('email_verified_at', '>=', $date))
        //     ->when($this->getFilter('verified_to'), fn($query, $date) => $query->where('email_verified_at', '<=', $date));

    }

    // public function delete(): void
    // {
    //     if ($this->selectedRowsQuery->count() > 0) {
    //         Portfolio::whereIn('id', $this->selectedKeys())->delete();
    //     }

    //     $this->selected = [];

    //     $this->resetBulk();
    // }

    public function getTableRowUrl($row): string
    {
        return route('holding.show', ['holding' => $row->id, 'portfolio' => $row->portfolio_id]);
    }
}
