<?php

namespace App\Repositories;

use App\Classes\MyHelpers;
use App\Interfaces\UserInterface;
use App\Models\JobPosition;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(User $model)
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
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    /**
     * Mendapatkan data absensi berdasarkan ID.
     */
    public function getById($id)
    {
        // return $this->model::with('user', 'schedule', 'attendance')->findOrFail($id);
    }

    /**
     * Menyimpan data absensi baru.
     */
    public function store($data)
    {
        $org = Organization::updateOrCreate(
            ['name'=>$data['dept_name']],
            [
                'name'=>$data['dept_name'],
                'description'=>$data['dept_name']. ' information organization.'
            ]
        );
        $user = $this->model::updateOrCreate(
            ['nik'=>$data['emp_code']],
            [
                'name'=>$data['full_name'],
                'nik'=>$data['emp_code'],
                'email'=>$data['emp_code'].$data['full_name'].'@sinergiabadisentosa.com',
                'password'=>Hash::make('emp_code')
            ]
        );
        if (!empty($data['position_name'])) {
            $position = JobPosition::updateOrCreate(
                ['name'=>$data['position_name']],
                [
                    'name'=>$data['position_name'],
                    'description'=>$data['position_name']. ' information organization.'
                ]
            );
            $user->employe()->updateOrCreate(
                [
                    'organization_id'=>$org->id, 
                    'job_position_id'=>$position->id,
                    'job_level_id'=>1,
                    'approval_line'=>1,
                    'approval_manager'=>1,
                    'company_id'=>1,
                    'branch_id'=>1,
                ],
                [
                    'organization_id'=>$org->id, 
                    'job_position_id'=>$position->id,
                    'job_level_id'=>1,
                    'approval_line'=>1,
                    'approval_manager'=>1,
                    'company_id'=>1,
                    'branch_id'=>1,
                ]
            );
        }else{
            $user->employe()->updateOrCreate(
                [
                    'organization_id'=>$org->id, 
                    'job_position_id'=>1,
                    'job_level_id'=>1,
                    'approval_line'=>1,
                    'approval_manager'=>1,
                    'company_id'=>1,
                    'branch_id'=>1,
                ],
                [
                    'organization_id'=>$org->id, 
                    'job_position_id'=>1,
                    'job_level_id'=>1,
                    'approval_line'=>1,
                    'approval_manager'=>1,
                    'company_id'=>1,
                    'branch_id'=>1,
                ]
            );
        }
        return $user;
    }

    /**
     * Memperbarui data absensi.
     */
    public function update($data, $id)
    {
        // $q = $this->model::find($id);
        // $q->lat = $data['in']['lat'];
        // $q->lng = $data['in']['lng'];
        // $q->time = $data['in']['time'];
        // $q->save();
        // $q->attendance()->updateOrCreate(
        //     [
        //         'in_attendance_id'=> $id,
        //     ],
        //     [
        //         'nik'=> $q->nik,
        //         'schedule_group_attendances_id'=> $q->schedule_group_attendances_id,
        //         'lat'=> $data['out']['lat'],
        //         'lng'=> $data['out']['lng'],
        //         'time'=> $data['out']['time'],
        //         'date'=> $q->date
        //     ]
        // );
        // return $q;
    }

    /**
     * Menghapus data absensi.
     */
    public function delete($id)
    {
        $this->model::destroy($id);
    }
}
