<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rebates', function (Blueprint $table) {
            // Student_fees_id
            $table->unsignedBigInteger('student_fees_id')->primary();
            // $table->string('url');
            $table->unsignedTinyInteger('days_applied');
            $table->unsignedTinyInteger('days_approved')->default(0);

            $table->foreign('student_fees_id')->references('id')->on('student_fees')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebates');
    }
};
