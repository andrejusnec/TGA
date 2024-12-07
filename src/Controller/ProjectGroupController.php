<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectGroupRepository;
use App\Service\ProjectGroupServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/group')]
final class ProjectGroupController extends AbstractController
{
    public function __construct(
        private readonly ProjectGroupServiceInterface $projectGroupService,
        private readonly ProjectGroupRepository $projectGroupRepository
    ) {
    }

    #[Route(name: 'app_project_group_index', methods: ['GET'])]
    public function index(): Response
    {
        $projectGroups = $this->projectGroupRepository->findAll();

        return $this->render('project_group/index.html.twig', [
            'project_groups' => $projectGroups,
        ]);
    }

    #[Route('/{groupId}/assign', name: 'app_group_assign_student', methods: ['POST'])]
    public function assignStudent(Request $request, int $groupId): JsonResponse
    {
        $studentId = (int) $request->request->get('student');

        $result = $this->projectGroupService->assignStudentToGroup($studentId, $groupId);

        return new JsonResponse($result, $result['success'] ? 200 : 400);
    }

    #[Route('/{groupId}/remove', name: 'app_group_remove_student', methods: ['POST'])]
    public function removeStudent(Request $request, int $groupId): JsonResponse
    {
        $studentId = (int) $request->request->get('student');

        $responseData = $this->projectGroupService->removeStudentAndUpdateGroup($studentId, $groupId);

        return $this->json([
            'success' => true,
            'studentId' => $responseData['studentId'],
            'studentName' => $responseData['studentName'],
            'currentStudentCount' => $responseData['currentStudentCount'],
            'maxStudentsPerGroup' => $responseData['maxStudentsPerGroup'],
        ]);
    }
}
