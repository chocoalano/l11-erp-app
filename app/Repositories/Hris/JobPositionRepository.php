<?php

namespace App\Repositories\Hris;

use App\Interfaces\Hris\JobPositionInterface;
use App\Models\JobPosition;

class JobPositionRepository implements JobPositionInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(JobPosition $model)
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
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        $data = $query->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }

    /**
     * Mendapatkan data absensi berdasarkan ID.
     */
    public function getById($id)
    {
        return $this->model::findOrFail($id);
    }

    /**
     * Menyimpan data absensi baru.
     */
    public function store($data)
    {
        $q = $this->model::updateOrCreate(
            [
                'name'=>$data['position_name'],
                'description'=>$data['position_name'].' information dummy description.'
            ],
            [
                'name'=>$data['position_name'],
                'description'=>$data['position_name'].' information dummy description.'
            ]
        );
        return $q;
    }

    /**
     * Memperbarui data absensi.
     */
    public function update($data, $id)
    {
        return $data;
    }

    /**
     * Menghapus data absensi.
     */
    public function delete($id)
    {
        $this->model::destroy($id);
    }
}
