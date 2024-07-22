<?php

namespace App\Interfaces\Marketing;

interface AwardInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function getAllActiveStatus($perPage, $page, $search);
    public function getFromSlugData(string $slug);
    public function getSeoPage();
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
