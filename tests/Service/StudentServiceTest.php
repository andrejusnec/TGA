<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Student;
use App\Entity\ProjectGroup;
use App\Factory\StudentFactoryInterface;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Service\StudentService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentServiceTest extends TestCase
{
    private StudentRepository $studentRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->studentRepository = $this->createMock(StudentRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testDeleteStudentRemovesStudentAndUpdatesGroup(): void
    {
        $student = new Student();
        $group = new ProjectGroup();
        $group->addStudent($student);
        $student->setProjectGroup($group);

        $this->studentRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($student);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($group);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($student);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $service = $this->createService();

        $service->deleteStudent(1);

        $this->assertFalse($group->getStudents()->contains($student));
    }

    public function testDeleteStudentWithoutGroupDoesNotPersistGroup(): void
    {
        $student = new Student();

        $this->studentRepository->expects($this->once())
            ->method('find')
            ->with(2)
            ->willReturn($student);

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($student);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $service = $this->createService();

        $service->deleteStudent(2);

        $this->assertNull($student->getProjectGroup());
    }

    public function testDeleteStudentThrowsNotFoundException(): void
    {
        $this->studentRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $service = $this->createService();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Student not found.');

        $service->deleteStudent(999);
    }

    public function testDeleteStudentFromGroupWithOtherStudents(): void
    {
        $studentToRemove = new Student();
        $remainingStudent = new Student();

        $group = new ProjectGroup();
        $group->addStudent($studentToRemove);
        $group->addStudent($remainingStudent);

        $studentToRemove->setProjectGroup($group);
        $remainingStudent->setProjectGroup($group);

        $this->studentRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($studentToRemove);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($group);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($studentToRemove);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $service = $this->createService();

        $service->deleteStudent(1);

        $this->assertFalse(
            $group->getStudents()->contains($studentToRemove),
            'The student being removed should no longer belong to the group'
        );

        $this->assertTrue(
            $group->getStudents()->contains($remainingStudent),
            'Other students in the group should remain unaffected'
        );
    }

    private function createService(): StudentService
    {
        return new StudentService(
            $this->studentRepository,
            $this->entityManager,
            $this->createMock(StudentFactoryInterface::class),
            $this->createMock(ValidatorInterface::class),
            10
        );
    }
}
