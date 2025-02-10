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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('mess_fee_id');
            $table->string('student_roll_number');
            $table->string('invoice_date');
            $table->string('total_amount');
            $table->string('status');

            $table->foreign('mess_fee_id')->references('id')->on('mess_fees')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('student_roll_number')->references('roll_number')->on('students')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('invoices');
    }
};
