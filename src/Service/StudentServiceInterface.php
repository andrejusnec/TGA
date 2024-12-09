<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;

interface StudentServiceInterface
{
    public function getPaginatedAndUnassignedStudents(int $page): array;

    public function createStudentFromRequestData(array $data): Student;

    public function deleteStudent(int $id): void;
}