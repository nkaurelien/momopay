<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMomoTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('momo_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_id');
            $table->string('transaction_id');
            $table->string('transaction_status')->comment('pending,failed,successful');
            $table->json('payment_result')->nullable();
            $table->string('verified_at')->nullable()->comment('last verification date');
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
        Schema::dropIfExists('momo_transactions');
    }
}
