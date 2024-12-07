<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProjectGroupRepository;
use App\Repository\StudentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProjectGroupService implements ProjectGroupServiceInterface
{

    private const GROUP_NAME = 'groupName';
    private const STUDENT_NAME = 'studentName';

    public function __construct(
        private readonly ProjectGroupRepository $projectGroupRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function assignStudentToGroup(int $studentId, int $groupId): array
    {
        $group = $this->projectGroupRepository->find($groupId);
        $student = $this->studentRepository->find($studentId);

        if (!$group || !$student) {
            return $this->returnErrorResponse('Invalid group or student.');
        }

        $currentProject = $student->getProjectGroup()?->getProject();
        if ($currentProject && $currentProject !== $group->getProject()) {
            return $this->returnErrorResponse('This student is already assigned to another project.');
        }

        $student->setProjectGroup($group);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $this->returnSuccessResponse(self::GROUP_NAME, $group->getName(), $student->getId());
    }

    public function removeStudentFromGroup(int $studentId, int $groupId): array
    {
        $group = $this->projectGroupRepository->find($groupId);
        $student = $this->studentRepository->find($studentId);

        if (!$group || !$student) {
            return $this->returnErrorResponse('Invalid group or student.');
        }

        if (!$group->getStudents()->contains($student)) {
            return $this->returnErrorResponse('Student not in group.');
        }

        $group->removeStudent($student);
        $this->entityManager->flush();

        return $this->returnSuccessResponse(self::STUDENT_NAME, $student->getFullName(), $student->getId());
    }

    private function returnSuccessResponse(string $argument, string $value, int $studentId): array
    {
        return [
            'success' => true,
            'studentId' => $studentId,
            $argument => $value,
        ];
    }

    private function returnErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
