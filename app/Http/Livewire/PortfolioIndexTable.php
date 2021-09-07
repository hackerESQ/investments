<?php

namespace App\Http\Livewire;

use App\Models\Portfolio;
use App\Traits\FormatsMoney;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
// use Rappasoft\LaravelLivewireTables\Views\Filter;

class PortfolioIndexTable extends DataTableComponent
{

    use FormatsMoney;
    
    public bool $columnSelect = true;
    public string $defaultSortColumn = 'id';
    public bool $reorderEnabled = false;
    public bool $hideBulkActionsOnEmpty = true;
    public array $bulkActions = [
        'delete'   => 'Delete',
    ];

    public function columns(): array
    {
        return [
            Column::make('Id')
                  ->sortable(),
            Column::make('Title')
                  ->searchable(),
            Column::make('Notes')
                  ->searchable(),
            Column::make('Total Cost Basis', 'holdings_sum_total_cost_basis')
                  ->format(function ($value, $column, $row) {
                      return $this->formatMoney($value);
                  }),
            Column::make('Dividends Earned', 'holdings_sum_dividends_earned')
                  ->format(function ($value, $column, $row) {
                      return $this->formatMoney($value);
                  }),
            Column::make('Updated At'),
            Column::make('Created At'),
            // Column::make('Active')
            //       ->sortable()
            //       ->format(function ($value) {
            //           return view('tables.cells.boolean',
            //               [
            //                   'boolean' => $value
            //               ]
            //           );
            //       })
            //       ,
            // Column::make('Verified', 'email_verified_at')
            //       ->sortable()
            //       ->excludeFromSelectable(),
        ];
    }

    // public function filters(): array
    // {
    //     return [
    //         'verified' => Filter::make('E-mail Verified')
    //             ->select([
    //                 ''    => 'Any',
    //                 'yes' => 'Yes',
    //                 'no'  => 'No',
    //             ]),
    //         'active'   => Filter::make('Active')
    //             ->select([
    //                 ''    => 'Any',
    //                 'yes' => 'Yes',
    //                 'no'  => 'No',
    //             ]),
    //         'verified_from' => Filter::make('Verified From')
    //             ->date(),
    //         'verified_to' => Filter::make('Verified To')
    //             ->date(),
    //     ];
    // }

    public function query()
    {
        return Portfolio::query()
            ->withSum('holdings', 'total_cost_basis')
            ->withSum('holdings', 'dividends_earned');
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

    public function delete(): void
    {
        if ($this->selectedRowsQuery->count() > 0) {
            Portfolio::whereIn('id', $this->selectedKeys())->delete();
        }

        $this->selected = [];

        $this->resetBulk();
    }

    public function getTableRowUrl($row): string
    {
        return route('portfolio.show', $row->id);
    }
}