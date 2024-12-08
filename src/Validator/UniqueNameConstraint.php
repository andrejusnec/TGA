<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueNameConstraint extends Constraint
{
    public string $message = 'A student with the name "{{ name }}" and surname "{{ surname }}" already exists.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueNameValidator::class;
    }
}