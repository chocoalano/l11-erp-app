<?php

namespace App\Filament\Resources\ScheduleGroupAttendanceResource\Pages;

use App\Filament\Resources\ScheduleGroupAttendanceResource;
use App\Models\ScheduleGroupAttendance;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ManageScheduleGroupAttendances extends ManageRecords
{
    protected static string $resource = ScheduleGroupAttendanceResource::class;

    protected function formatDate($date)
    {
        $dateObj = DateTime::createFromFormat('d/m/Y', $date);
        if (!$dateObj) {
            throw new \Exception("Format tanggal tidak valid");
        }
        return $dateObj->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->mutateFormDataUsing(function (array $data): array {
                $dates = explode(" - ", $data['date']);
                if (count($dates) !== 2) {
                    throw new \Exception("Input tanggal harus berisi dua tanggal yang dipisahkan dengan ' - '");
                }
                list($start_date, $end_date) = $dates;
                $start_date = $this->formatDate($start_date);
                $end_date = $this->formatDate($end_date);

                $date_array = [];
                $start = new Carbon($start_date);
                $end = new Carbon($end_date);
                for ($date = $start; $date->lte($end); $date->addDay()) {
                    $date_array[] = $date->format('Y-m-d');
                }

                $data_return = [];
                foreach ($date_array as $k) {
                    $data_return[] = [
                        'group_attendance_id' => $data['group_attendance_id'],
                        'time_attendance_id' => $data['time_attendance_id'],
                        'date' => $k,
                    ];
                }
                return $data_return;
            })
            ->using(function (array $data): Model {
                foreach ($data as $entry) {
                    $model = new ScheduleGroupAttendance($entry);
                    $model->save();
                }
                return $model;
            }),
        ];
    }
}
