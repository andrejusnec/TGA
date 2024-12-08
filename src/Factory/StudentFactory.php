<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Student;

class StudentFactory implements StudentFactoryInterface
{
    public function createStudentWithData(array $data): Student
    {
        $student = $this->createStudent();
        $student->setName($data['name']);
        $student->setSurname($data['surname']);

        return $student;
    }

    public function createStudent(): Student
    {
        return new Student();
    }
}