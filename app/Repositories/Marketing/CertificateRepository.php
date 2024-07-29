<?php

namespace App\Repositories\Marketing;

use App\Interfaces\Marketing\CertificateInterface;
use App\Models\Marketing\Digital\Certificate;
use App\Models\Marketing\Digital\Meta;

class CertificateRepository implements CertificateInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(Certificate $model)
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
    public function getAllActiveStatus($perPage, $page, $search)
    {
        $query = $this->model::query();
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                    ->where('active', true)
                    ->orWhere('content', 'like', "%{$search}%");
        }
        $data = $query->where('active', true)->paginate($perPage, ['*'], 'page', $page);
        return $data;
    }
    public function getFromSlugData($slug)
    {
        $slugString = str_replace('-', ' ', $slug);
        return $this->model::with('item')->where('title', $slugString)->first();
    }
    public function getSeoPage()
    {
        return Meta::where('page', 'certificate')->first();
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
