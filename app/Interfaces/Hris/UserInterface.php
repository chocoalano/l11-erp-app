<?php

namespace App\Interfaces\Hris;

interface UserInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
