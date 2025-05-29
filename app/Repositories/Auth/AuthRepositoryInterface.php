<?php

namespace App\Repositories\Auth;

interface AuthRepositoryInterface
{
    public function register(array $data);
    public function login(array $data);
    public function logout($user);
    public function getProfile($user);
    public function updateProfile($user, array $data);
}