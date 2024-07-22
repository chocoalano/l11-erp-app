<?php

namespace App\Repositories\Marketing;

use App\Interfaces\Marketing\SosmedInterface;
use App\Models\Marketing\Compro\Contact;
use App\Models\Marketing\Compro\Sosmed;

class SosmedRepository implements SosmedInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(Sosmed $model)
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
        ->get();
        return $query;
    }
    public function getAllContactActiveStatus()
    {
        $query = Contact::query()
        ->where('active', true)
        ->get();
        return $query;
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
