<?php

namespace App\Jobs;

use App\Models\ScheduleGroupAttendance;
use App\Models\User;
use App\Classes\MyHelpers;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable as QueueQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImportScheduleFromBiotime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, QueueQueueable, SerializesModels;

    protected $data;
    public $tries = 5; // Set jumlah maksimal percobaan
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
    public function handle(): bool
    {
        $helper = new MyHelpers();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        try {
            collect($this->data)->chunk(10)->each(function ($chunk) use ($helper, $startDate, $endDate) {
                foreach ($chunk as $item) {
                    $nik = $item[0];
                    $scheduleData = array_slice($item, 4);
                    $currentDate = $startDate->copy();
                    
                    $user = User::with('employe', 'group_attendance')->where('nik', $nik)->first();

                    if (!$user || !$user->employe || !$user->group_attendance) {
                        Log::warning('User or related data not found', ['nik' => $nik]);
                        continue;
                    }

                    foreach ($scheduleData as $jam) {
                        if ($jam !== 'Day Off') {
                            $cekJam = $helper->syncJamJadwalKerja($user->employe->organization_id, $jam);

                            ScheduleGroupAttendance::updateOrCreate([
                                'group_attendance_id' => $user->group_attendance->first()->id,
                                'user_id' => $user->id,
                                'time_attendance_id' => $cekJam->id,
                                'date' => $currentDate->toDateString()
                            ]);

                            $currentDate->addDay();
                            if ($currentDate->greaterThan($endDate)) {
                                break;
                            }
                        }
                    }
                }
            });

            return true; // Return true jika proses berhasil
        } catch (\Exception $e) {
            Log::error('An error occurred while processing the schedule', [
                'exception' => $e->getMessage()
            ]);

            return false; // Return false jika terjadi kesalahan
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
    }
}
