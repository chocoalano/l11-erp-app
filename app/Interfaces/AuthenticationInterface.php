<?php

namespace App\Interfaces;

interface AuthenticationInterface
{
    public function login($emailOrNik, $password);
    public function profile();
    public function updateProfile(array $data);
    public function logout($tokenId);
}
