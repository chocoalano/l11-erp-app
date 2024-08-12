<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessImportUserBiotime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    public $tries = 5; // Set jumlah maksimal percobaan
    public $timeout = 300; // Set waktu timeout dalam detik

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
        collect($this->data)->chunk(100)->each(function ($chunk) use ($helper) {
            $validator = Validator::make($chunk->toArray(), [
                'nik' => 'required|numeric|digits:8',
                'nama' => 'required|string|max:255',
                'dept' => 'required|string|max:50',
                'position' => 'required|string|max:50',
                'level' => 'required|string|max:50',
                'atasan' => 'required|string|max:255',
                'grade' => 'required|string|max:50',
                'emp_status' => 'required|string|in:AKTIF,TIDAK AKTIF',
                'area_kerja' => 'required|string|max:50',
                'tgl_bergabung' => 'required|date_format:Y-m-d',
                'no_ktp' => 'required|numeric|digits:16',
                'no_npwp' => 'nullable|string|regex:/^\d{9}-\d{3}\.\d{3}$/',
                'no_hp' => 'required|numeric|digits_between:10,15',
                'email' => 'required|email|max:255',
                'placebirth' => 'required|string|max:100',
                'datebirth' => 'nullable|date_format:Y-m-d',
                'religion' => 'required|string|max:50',
                'gender' => 'required|string|in:LAKI-LAKI,PEREMPUAN',
                'status_pernikahan' => 'nullable|string|in:MENIKAH,BELUM MENIKAH', 
            ]);
            if ($validator->fails()) {
                Log::error('Validation failed for chunk', ['errors' => $validator->errors()]);
                return $validator->errors();
            }
            foreach ($chunk->toArray() as $k) {
                $helper->validateUserExist($k);
            }
        });
    }
    public function failed(\Exception $exception)
    {
        // Tangani kegagalan job di sini
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
    }
}
