<?php

namespace App\Jobs;

use App\Models\Student_mess_fee;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddMessFineToStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;  // Retry up to 3 times
    public $timeout = 3600;  // Optional: Set a timeout for long-running jobs

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                // Lock the rows to prevent race conditions
                $pendingFees = Student_mess_fee::where('status', 'pending')
                    ->whereHas('messFee', function ($query) {
                        $query->where('due_date', '<', Carbon::now());
                    })
                    ->with('messFee')
                    ->lockForUpdate() // Lock the selected rows
                    ->get();

                foreach ($pendingFees as $fee) {
                    try {
                        $dueDate = $fee->messFee->due_date;
                        $daysOverdue = Carbon::parse($dueDate)->diffInDays(Carbon::now());
                        $fineAmount = $daysOverdue * $fee->messFee->fine_per_day;

                        // Update total fee
                        $fee->update([
                            'total_fee' => $fee->total_fee + $fineAmount,
                        ]);
                    } catch (Exception $e) {
                        // Log the error for this specific student but continue processing others
                        Log::error("Failed to update fine for student {$fee->student_roll_number}: " . $e->getMessage());
                    }
                }
            });
        } catch (Exception $e) {
            // Log the critical error and rollback the entire transaction
            Log::critical("Batch job failed: " . $e->getMessage());
        }
    }
}
