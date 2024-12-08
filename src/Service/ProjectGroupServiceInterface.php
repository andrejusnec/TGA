<?php

declare(strict_types=1);

namespace App\Service;

interface ProjectGroupServiceInterface
{
    public function assignStudentToGroup(int $studentId, int $groupId): array;

    public function removeStudentAndUpdateGroup(int $studentId, int $groupId): array;
}