<?php

namespace App\Model\User\UseCase\Name;

use Symfony\Component\Validator\Constraints\NotBlank;

class Command
{
    #[NotBlank]
    public string $id;

    #[NotBlank]
    public string $firstName;

    #[NotBlank]
    public string $lastName;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
