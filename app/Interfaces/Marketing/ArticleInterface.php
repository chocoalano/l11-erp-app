<?php

namespace App\Interfaces\Marketing;

interface ArticleInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function getAllActiveStatus($perPage, $page, $search);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
