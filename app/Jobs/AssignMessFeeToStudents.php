<?php

namespace App\Jobs;

use App\Models\Mess_fee;
use App\Models\Student;
use App\Models\Student_mess_fee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AssignMessFeeToStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $messFee;
    public $tries = 3;
    public $maxExceptions = 3;

    public function __construct(Mess_fee $messFee)
    {
        //
        $this->messFee = $messFee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
            // Using chunk to handle large datasets in smaller batches
            Student::chunk(100, function ($students) {
                foreach ($students as $student) {
                    try {
                        // Insert student fee record for each student
                        Student_mess_fee::create([
                            'mess_fee_id' => $this->messFee->id,
                            'student_roll_number' => $student->roll_number,
                            'status' => 'pending',
                            'total_fee' => $this->messFee->total_fee
                        ]);
                    } catch (Throwable $e) {
                        // Log the error for individual student insertion
                        Log::error("Failed to assign mess fee to student {$student->roll_number}: " . $e->getMessage());
                    }
                }
            });
        } catch (Throwable $e) {
            Log::error('Failed to assign mess fee to students: ' . $e->getMessage());
            throw $e;  // Re-throwing exception to indicate failure
        }
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        Log::error('Failed to assign mess fee to students: ' . $exception->getMessage());
    }
}
