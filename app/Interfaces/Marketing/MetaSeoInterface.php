<?php

namespace App\Interfaces\Marketing;

interface MetaSeoInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function getByPage($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
