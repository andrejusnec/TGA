<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StudentRepository extends ServiceEntityRepository implements StudentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function getPaginatedStudents(int $page, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('student');

        return $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult();
    }

    public function countTotalStudents(): int
    {
        return $this->createQueryBuilder('student')
            ->select('COUNT(student.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
