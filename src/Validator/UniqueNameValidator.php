<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Student;
use App\Repository\StudentRepositoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueNameValidator extends ConstraintValidator
{
    public function __construct(private readonly StudentRepositoryInterface $studentRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueNameConstraint) {
            throw new UnexpectedTypeException($constraint, UniqueNameConstraint::class);
        }

        if (!$value instanceof Student) {
            throw new UnexpectedTypeException($value, Student::class);
        }

        $name = $value->getName() ?? null;
        $surname = $value->getSurname() ?? null;
        if (!$name || !$surname) {
            return;
        }

        $existingStudent = $this->studentRepository->findOneBy([
            'name' => $name,
            'surname' => $surname,
        ]);

        if ($existingStudent) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $name)
                ->setParameter('{{ surname }}', $surname)
                ->addViolation();
        }
    }
}