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
            $table->bigInteger('current_market_value');
            $table->bigInteger('total_gain_loss');
            $table->bigInteger('daily_gain_loss');
            $table->bigInteger('total_cost_basis');
            $table->bigInteger('daily_cost_basis_delta');
            $table->bigInteger('total_dividends');
            $table->bigInteger('realized_gains');
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
