<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\StudentRepository;

class StudentService implements StudentServiceInterface
{
    public function __construct(
        private readonly StudentRepository $studentRepository,
        private readonly int $studentPaginationLimit
    ) {
    }

    public function getPaginatedAndUnassignedStudents(int $page): array
    {
        $totalStudents = $this->studentRepository->countTotalStudents();

        return [
            'paginatedStudents' => $this->studentRepository->getPaginatedStudents($page, $this->studentPaginationLimit),
            'totalPages' => (int) ceil($totalStudents / $this->studentPaginationLimit),
            'unassignedStudents' => $this->studentRepository->findBy(['projectGroup' => null]),
        ];
    }
}