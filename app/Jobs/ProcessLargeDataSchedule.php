<?php

namespace App\Jobs;

use App\Models\ScheduleGroupAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessLargeDataSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $data;
    public $tries = 10; // Set jumlah maksimal percobaan
    public $timeout = 0; // Set waktu timeout dalam detik
    
    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Proses data besar dalam chunk
        collect($this->data)->chunk(100)->each(function ($chunk) {
            foreach ($chunk->toArray() as $k) {
                ScheduleGroupAttendance::updateOrCreate([
                    'user_id'=>$k['user_id'],
                    'date'=>$k['date'],
                    'group_attendance_id'=>$k['group_attendance_id'],
                ], $k);
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        // Tangani kegagalan job di sini
        dd($exception->getMessage());
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
    }
}
