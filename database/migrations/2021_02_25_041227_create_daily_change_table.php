<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_change', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreign('user_id')->constrained()->onDelete('cascade');
            $table->float('total_market_value', 12, 4);
            $table->float('total_cost_basis', 12, 4);
            $table->float('total_gain_loss', 12, 4);
            $table->float('total_dividends', 12, 4);
            $table->float('realized_gains', 12, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_change');
    }
}
