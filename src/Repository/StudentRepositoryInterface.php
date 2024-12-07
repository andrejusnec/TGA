<?php

declare(strict_types=1);

namespace App\Repository;

interface StudentRepositoryInterface
{
    public function getPaginatedStudents(int $page, int $limit = 10): array;

    public function countTotalStudents(): int;
}