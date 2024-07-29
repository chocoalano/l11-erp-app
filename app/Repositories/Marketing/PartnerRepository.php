<?php

namespace App\Repositories\Marketing;

use App\Interfaces\Marketing\PartnerInterface;
use App\Models\Marketing\Digital\Meta;
use App\Models\Marketing\Digital\Partner;

class PartnerRepository implements PartnerInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(Partner $model)
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
        return $this->model::findOrFail($id);
    }
    public function getAllActiveStatus()
    {
        $query = $this->model::query()
        ->where('active', true)
        ->first();
        return $query;
    }
    public function getSeoPage()
    {
        return Meta::where('page', 'partner')->first();
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
