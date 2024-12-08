<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;
use App\Factory\StudentFactoryInterface;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentService implements StudentServiceInterface
{
    public function __construct(
        private readonly StudentRepository $studentRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly StudentFactoryInterface $studentFactory,
        private readonly ValidatorInterface $validator,
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

    private function validateStudent(Student $student): void
    {
        $violations = $this->validator->validate($student);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            throw new BadRequestHttpException(json_encode(['errors' => $errors]));
        }
    }

    public function createStudentFromRequestData(array $data): Student
    {
        if (!isset($data['name']) || !isset($data['surname'])) {
            throw new BadRequestHttpException(json_encode([
                'errors' => [
                    [
                        'field' => 'name',
                        'message' => 'Both name and surname are required.'
                    ],
                ]
            ]));
        }

        $student = $this->studentFactory->createStudentWithData($data);

        $this->validateStudent($student);

        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $student;
    }

    public function deleteStudent(int $id): void
    {
        $student = $this->studentRepository->find($id);

        if (!$student) {
            throw new NotFoundHttpException('Student not found.');
        }

        if ($student->getProjectGroup()) {
            $group = $student->getProjectGroup();
            $group->removeStudent($student);
            $this->entityManager->persist($group);
        }

        $this->entityManager->remove($student);
        $this->entityManager->flush();
    }
}