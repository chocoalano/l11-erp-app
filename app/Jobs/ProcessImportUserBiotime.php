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
    public function handle(): bool
    {
        $helper = new \App\Classes\MyHelpers();
        $validationErrors = [];

        collect($this->data)->chunk(50)->each(function ($chunk) use ($helper, &$validationErrors) {
            $chunk->each(function ($item) use ($helper, &$validationErrors) {
                // $validator = Validator::make($item, [
                //     'nik' => 'required|numeric|digits:8',
                //     'nama' => 'required|string|max:255',
                //     'dept' => 'required|string|max:50',
                //     'position' => 'required|string|max:50',
                //     'level' => 'required|string|max:50',
                //     'atasan' => 'required',
                //     'grade' => 'required|string|max:50',
                //     'emp_status' => 'required|string|in:AKTIF,TIDAK AKTIF',
                //     // 'area_kerja' => 'required|string|max:50',
                //     'tgl_bergabung' => 'required|date_format:Y-m-d',
                //     // 'no_ktp' => 'required|numeric|digits:16',
                //     // 'no_npwp' => 'nullable|string|regex:/^\d{9}-\d{3}\.\d{3}$/',
                //     'no_hp' => 'required|digits_between:10,15',
                //     'email' => 'required|email|max:255',
                //     'placebirth' => 'required|string|max:100',
                //     'datebirth' => 'nullable|date_format:Y-m-d',
                //     'religion' => 'required|string|max:50',
                //     'gender' => 'required|string|in:LAKI-LAKI,PEREMPUAN',
                //     'status_pernikahan' => 'nullable|string|in:MENIKAH,LAJANG,CERAI',
                // ]);

                // if ($validator->fails()) {
                //     $validationErrors[] = $validator->errors()->all();
                //     return; // Lanjutkan ke item berikutnya
                // }

                // Jika validasi berhasil, lakukan proses selanjutnya
                $helper->validateUserExist($item);
            });
        });

        if (!empty($validationErrors)) {
            Log::error('Validation errors', ['errors' => $validationErrors]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception)
    {
        Log::error('Job failed', ['exception' => $exception->getMessage()]);
        // Anda bisa melakukan tindakan lain jika terjadi kegagalan
    }
}
