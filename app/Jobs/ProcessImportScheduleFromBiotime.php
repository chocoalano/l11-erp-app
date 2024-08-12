<?php

namespace App\Jobs;

use App\Models\ScheduleGroupAttendance;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessImportScheduleFromBiotime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
    public function handle(): void
    {
        $helper = new \App\Classes\MyHelpers();
        collect($this->data)->chunk(5)->each(function ($chunk) use ($helper) {
            $data = $chunk->toArray();
            // Mendapatkan tanggal awal dan akhir bulan saat ini
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            foreach ($data as $item) {
                $nik = $item[0];
                $scheduleData = array_slice($item, 4); // Ambil data jadwal mulai dari indeks ke-4
                $currentDate = $startDate->copy();
                foreach ($scheduleData as $index => $jam) {
                    if ($jam !== 'Day Off') { // Skip entri dengan tipe "Day Off"
                        $cekUser = $user = \App\Models\User::with('employe', 'group_attendance')->where('nik', $nik)->first();
                        $user = $cekUser;
                        $emp = $cekUser->employe;
                        $group = $cekUser->group_attendance;
                        $cekJam = $helper->syncJamJadwalKerja($emp->organization_id, $jam);
                        if (!is_null($emp) && !is_null($group)) {
                            $fetch = [
                                'group_attendance_id'=>$group[0]->id,
                                'user_id'=>$user->id,
                                'time_attendance_id'=>$cekJam->id,
                                'date'=>$currentDate->toDateString()
                            ];
                            ScheduleGroupAttendance::updateOrCreate($fetch, $fetch);
                        }
                    }
                    // Tambahkan satu hari ke currentDate
                    $currentDate->addDay();
                    // Hentikan jika sudah melewati akhir bulan
                    if ($currentDate->greaterThan($endDate)) {
                        break;
                    }
                }
            }
        });
    }

    public function failed(\Exception $exception)
    {
        // Tangani kegagalan job di sini
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
        dd($exception);
    }
}
