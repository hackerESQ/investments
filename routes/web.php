<?php

use App\Exports\BackupExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HoldingController,
    ImportExportController,
    PortfolioController,
    TransactionController
};
use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/portfolio', 301);

Route::middleware(['auth:sanctum', 'verified'])->group(function() {

    // backup
    Route::post('import', [ImportExportController::class, 'import'])->name('backup.import');
    Route::view('import', 'pages.backup')->name('backup.importForm');
    Route::get('export', [ImportExportController::class, 'export'])->name('backup.export');

    // portfolios resource
    Route::resource('portfolio', PortfolioController::class);

    // transactions resource
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'create'])->name('portfolio.transaction.create');
    Route::post('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'store'])->name('portfolio.transaction.store');
    // Route::get('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
    // Route::put('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');

    // holdings view
    Route::get('portfolio/{portfolio}/symbol/{holding:symbol}', [HoldingController::class, 'show'])->name('holding.show');

    Route::get('test', function(){



        \App\Models\Split::syncTransactions(['symbol' => 'AAPL']);







        // $test = \App\Models\Transaction::where('symbol', 'DSI')
        // ->selectRaw('MIN(date) as first_transaction')
        // ->first();

        // return (new DateTime($test->first_transaction))->format('y-m-d');


        // $date = \App\Models\Dividend::where(['symbol' => 'DSI'])
        //     ->selectRaw('MIN(date) as first_dividend_date')
        //     ->selectRaw('MAX(date) as last_dividend_date')
        //     ->get();
            
        // $date->get('first_dividend');
    });
});