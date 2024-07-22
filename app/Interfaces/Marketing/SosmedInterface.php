<?php

namespace App\Interfaces\Marketing;

interface SosmedInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function getAllActiveStatus();
    public function getAllContactActiveStatus();
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
