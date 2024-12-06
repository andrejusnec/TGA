<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectGroupRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/group')]
final class ProjectGroupController extends AbstractController
{
    #[Route(name: 'app_project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository): Response
    {
        return $this->render('project_group/index.html.twig', [
            'project_groups' => $projectGroupRepository->findAll(),
        ]);
    }

    #[Route('/{groupId}/assign', name: 'app_group_assign_student', methods: ['POST'])]
    public function assignStudent(
        Request $request,
        StudentRepository $studentRepository,
        ProjectGroupRepository $projectGroupRepository,
        EntityManagerInterface $entityManager,
        int $groupId): Response
    {
        $group = $projectGroupRepository->find($groupId);
        $studentId = $request->request->get('student');

        if ($group && $studentId) {
            $student = $studentRepository->find($studentId);
            if ($student && !$student->getProjectGroup()) {
                // Only assign if the student is not already assigned
                $student->setProjectGroup($group);
                $entityManager->persist($student);
                $entityManager->flush();
            }
        }

        return $this->json([
            'success' => true,
            'studentId' => $studentId,
        ]);
    }

    #[Route('/group/{groupId}/remove', name: 'app_group_remove_student', methods: ['POST'])]
    public function removeStudent(
        Request $request,
        StudentRepository $studentRepository,
        ProjectGroupRepository $projectGroupRepository,
        EntityManagerInterface $entityManager,
        int $groupId
    ): JsonResponse {
        $studentId = $request->request->get('student');
        $student = $studentRepository->find($studentId);
        $group = $projectGroupRepository->find($groupId);

        if (!$group || !$student) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid group or student.'], 400);
        }

        if (!$group->getStudents()->contains($student)) {
            return new JsonResponse(['success' => false, 'message' => 'Student not in group.'], 400);
        }

        $group->removeStudent($student);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'studentId' => $student->getId(), 'studentName' => $student->getFullName()]);
    }
}
