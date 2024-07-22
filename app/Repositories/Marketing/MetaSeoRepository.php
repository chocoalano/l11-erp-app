<?php

namespace App\Repositories\Marketing;

use App\Interfaces\Marketing\MetaSeoInterface;
use App\Models\Marketing\Compro\Meta;

class MetaSeoRepository implements MetaSeoInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(Meta $model)
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
            $query->where('title', 'like', "%{$search}%")
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
        return $this->model::find($id)->first();
    }
    public function getByPage($page)
    {
        return $this->model::where('page', $page)->first();
    }

    /**
     * Menyimpan data absensi baru.
     */
    public function store($data)
    {
        return $data;
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
