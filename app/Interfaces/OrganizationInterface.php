<?php

namespace App\Interfaces;

interface OrganizationInterface
{
    public function index($perPage, $page, $search);
    public function getById($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
