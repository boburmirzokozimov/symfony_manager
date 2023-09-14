<?php

namespace App\ReadModel\User;

class AuthView
{
    public ?string $name = null;
    public ?string $id = null;
    public ?string $email = '';
    public ?string $password = null;
    public ?string $role = null;
    public ?string $status = null;
}