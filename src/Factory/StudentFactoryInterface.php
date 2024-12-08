<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Student;

interface StudentFactoryInterface
{
    public function createStudentWithData(array $data): Student;

    public function createStudent(): Student;
}