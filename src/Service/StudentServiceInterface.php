<?php

declare(strict_types=1);

namespace App\Service;

interface StudentServiceInterface
{
    public function getPaginatedAndUnassignedStudents(int $page): array;
}