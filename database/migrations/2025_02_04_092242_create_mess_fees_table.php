<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mess_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month'); 
            $table->year('year'); 
            $table->decimal('fee_per_day', 10, 2); 
            $table->unsignedTinyInteger('days_in_month'); 
            $table->decimal('total_fee', 10, 2);
            $table->decimal('fine_per_day');
            $table->datetime('due_date');
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
        Schema::dropIfExists('mess_fees');
    }
};
