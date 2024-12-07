<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ProjectGroup;
use App\Entity\Student;
use App\Repository\ProjectGroupRepository;
use App\Repository\StudentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class ProjectGroupService implements ProjectGroupServiceInterface
{

    private const GROUP_NAME = 'groupName';

    public function __construct(
        private readonly ProjectGroupRepository $projectGroupRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function assignStudentToGroup(int $studentId, int $groupId): array
    {
        /** @var ProjectGroup $group */
        $group = $this->projectGroupRepository->find($groupId);
        /** @var Student $student */
        $student = $this->studentRepository->find($studentId);

        if (!$group || !$student) {
            return $this->returnErrorResponse('Invalid group or student.');
        }

        $currentProject = $student->getProjectGroup()?->getProject();
        if ($currentProject && $currentProject !== $group->getProject()) {
            return $this->returnErrorResponse('This student is already assigned to another project.');
        }

        if ($group->getStudents()->count() >= $group->getProject()?->getMaxStudentsPerGroup()) {
            return $this->returnErrorResponse('The group is full.');
        }

        $student->setProjectGroup($group);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return [
            'success' => true,
            'studentId' => $studentId,
            self::GROUP_NAME => $group->getName(),
        ];
    }

    private function removeStudentFromGroup(int $studentId, ProjectGroup $group): void
    {
        $student = $this->studentRepository->find($studentId);
        if (!$student) {
            throw new InvalidArgumentException("Student not found.");
        }

        if (!$group->getStudents()->contains($student)) {
            throw new InvalidArgumentException("Student is not part of this group.");
        }

        $group->removeStudent($student);
        $this->entityManager->persist($group);
        $this->entityManager->flush();
    }

    public function removeStudentAndUpdateGroup(int $studentId, int $groupId): array
    {
        $group = $this->projectGroupRepository->find($groupId);
        if (!$group) {
            throw new InvalidArgumentException('No group found.');
        }

        $this->removeStudentFromGroup($studentId, $group);

        $student = $this->studentRepository->find($studentId);

        return [
            'studentId' => $studentId,
            'studentName' => $student->getFullName(),
            'currentStudentCount' => $group->getStudents()->count(),
            'maxStudentsPerGroup' => $group->getProject()?->getMaxStudentsPerGroup(),
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
