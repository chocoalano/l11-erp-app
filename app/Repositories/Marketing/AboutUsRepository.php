<?php

namespace App\Repositories\Marketing;

use App\Interfaces\Marketing\AboutUsInterface;
use App\Models\Marketing\Compro\AboutUs;
use App\Models\Marketing\Compro\Meta;

class AboutUsRepository implements AboutUsInterface
{
    protected $model;
    /**
     * Create a new class instance.
     */
    public function __construct(AboutUs $model)
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
        return $this->model::whereNot('title', 'Tentang PT. SINERGI ABADI SENTOSA')->where('active', true)->get();
    }
    public function getIntroductionActiveStatus()
    {
        return $this->model::where('title', 'Tentang PT. SINERGI ABADI SENTOSA')->where('active', true)->first();
    }
    public function getSeoPage()
    {
        return Meta::where('page', 'about')->first();
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
