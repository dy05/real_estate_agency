<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 200)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(min: 3, max: 100)]
    public string $email = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 200)]
    public string $message = '';

    #[Assert\NotBlank]
    public string $service = '';
}