<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HoldingController,
    PortfolioController,
    TransactionController
};
use App\Interfaces\MarketData\MarketDataInterface;

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

    // portfolios resource
    Route::view('portfolio/import', 'pages.portfolios.import')->name('portfolio.importForm');
    Route::post('portfolio/import', [PortfolioController::class, 'import'])->name('portfolio.import');
    Route::resource('portfolio', PortfolioController::class);

    // transactions resource
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'create'])->name('portfolio.transaction.create');
    Route::post('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'store'])->name('portfolio.transaction.store');
    // Route::get('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
    // Route::put('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');

    // holdings view
    Route::get('portfolio/{portfolio}/holding/{holding}', [HoldingController::class, 'show'])->name('holding.show');

    Route::get('test', function(){
        return app(MarketDataInterface::class)->splits(
            "AAPL",
            new \DateTime("-5 years"),
            new \DateTime("today")
        );
    });
});