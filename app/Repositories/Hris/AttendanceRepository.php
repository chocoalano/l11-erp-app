<?php

namespace App\Repositories\Hris;

use App\Classes\MyHelpers;
use App\Interfaces\Hris\AttendanceInterface;
use App\Models\Attendance;

class AttendanceRepository implements AttendanceInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(Attendance $model)
    {
        $this->model = $model;
    }

    /**
     * Mendapatkan semua data absensi.
     */
    public function index($perPage, $page, $search)
    {
        $query = $this->model::query();
        if ($search) {
            $query->where('nik', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhere('time', 'like', "%{$search}%");
        }
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    /**
     * Mendapatkan data absensi berdasarkan ID.
     */
    public function getById($id)
    {
        return $this->model::with('user', 'schedule', 'attendance')->findOrFail($id);
    }

    /**
     * Menyimpan data absensi baru.
     */
    public function store($data)
    {
        // $myHelpers = new MyHelpers();
        // $validate = $myHelpers->cekStatusTelatAbsen($data['schedule_group_attendances_id'], $data['time'], $data['flag']);
        // $status = $validate['status'];
        // if ($data['flag'] === 'in') {
        //     $q = $this->model::updateOrCreate(
        //         [
        //             'nik' => $data['nik'],
        //             'date' => $data['date'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //         ],
        //         [
        //             'nik' => $data['nik'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //             'lat' => $data['lat'],
        //             'lng' => $data['lng'],
        //             'date' => $data['date'],
        //             'time' => $data['time'],
        //             // 'photo' => $data['photo'],
        //             'status' => $status,
        //         ]
        //     );

        //     $q->attendance()->updateOrCreate(
        //         [
        //             'nik' => $data['nik'],
        //             'date' => $data['date'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //         ],
        //         [
        //             'nik' => $data['nik'],
        //             'date' => $data['date'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //         ]
        //     );
        // }else{
        //     $q=$this->model::where('nik', $data['nik'])
        //         ->where('date', $data['date'])
        //         ->where('schedule_group_attendances_id', $data['schedule_group_attendances_id'])
        //         ->first();
        //     $q->attendance()->updateOrCreate(
        //         [
        //             'nik' => $data['nik'],
        //             'date' => $data['date'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //         ],
        //         [
        //             'nik' => $data['nik'],
        //             'schedule_group_attendances_id' => $data['schedule_group_attendances_id'],
        //             'lat' => $data['lat'],
        //             'lng' => $data['lng'],
        //             'date' => $data['date'],
        //             'time' => $data['time'],
        //             // 'photo' => $data['photo'],
        //             'status' => $status,
        //         ]
        //     );
        // }
        // return $q;
    }

    /**
     * Memperbarui data absensi.
     */
    public function update($data, $id)
    {
        $q = Attendance::find($id);
        $q->lat = $data['in']['lat'];
        $q->lng = $data['in']['lng'];
        $q->time = $data['in']['time'];
        $q->save();
        $q->attendance()->updateOrCreate(
            [
                'in_attendance_id'=> $id,
            ],
            [
                'nik'=> $q->nik,
                'schedule_group_attendances_id'=> $q->schedule_group_attendances_id,
                'lat'=> $data['out']['lat'],
                'lng'=> $data['out']['lng'],
                'time'=> $data['out']['time'],
                'date'=> $q->date
            ]
        );
        return $q;
    }

    /**
     * Menghapus data absensi.
     */
    public function delete($id)
    {
        $this->model::destroy($id);
    }
    
}
