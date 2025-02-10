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
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            // basic
            $table->unsignedBigInteger('mess_fee_id');
            $table->string('student_roll_number');
            $table->enum('status',['pending','paid']);
            $table->decimal('total_fee',10,2);
            
            $table->integer('rebate_in_days')->default(0);
            $table->enum('rebate_status',['pending','approved','rejected'])->default('pending');

            // razorpay
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            
            // foreign keys
            $table->foreign('student_roll_number')->references('roll_number')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('mess_fee_id')->references('id')->on('mess_fees')->onUpdate('cascade')->onDelete('cascade');

            // timestamps of the events
            $table->timestamp('payment_date');  // when student made the payment
            $table->timestamps();   // default
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_fees');
    }
};
