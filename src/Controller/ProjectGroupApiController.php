<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectGroupRepository;
use App\Service\ProjectGroupServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ProjectGroupApiController extends AbstractController
{
    public function __construct(
        private readonly ProjectGroupServiceInterface $projectGroupService,
    ) {
    }

    #[Route('/api/groups/{groupId}/students', name: 'api_group_students', methods: ['GET'])]
    public function getGroupStudentCount(ProjectGroupRepository $projectGroupRepository, int $groupId): JsonResponse
    {
        $group = $projectGroupRepository->find($groupId);

        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        return new JsonResponse([
            'groupId' => $group->getId(),
            'students' => $group->getStudents()->count(),
            'maxStudentsPerGroup' => $group->getProject()?->getMaxStudentsPerGroup(),
        ]);
    }

    #[Route('/api/groups/{groupId}/assign', name: 'app_group_assign_student', methods: ['POST'])]
    public function assignStudent(Request $request, int $groupId): JsonResponse
    {
        $studentId = (int) $request->request->get('student');

        $result = $this->projectGroupService->assignStudentToGroup($studentId, $groupId);

        return new JsonResponse($result, $result['success'] ? 200 : 400);
    }

    #[Route('/api/groups/{groupId}/remove', name: 'app_group_remove_student', methods: ['POST'])]
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

    #[Route('/api/groups/{id}', name: 'api_get_project_group', methods: ['GET'])]
    public function getProjectGroup(int $id, ProjectGroupRepository $projectGroupRepository): JsonResponse
    {
        $projectGroup = $projectGroupRepository->find($id);

        if (!$projectGroup) {
            return new JsonResponse(['error' => 'Project group not found'], Response::HTTP_NOT_FOUND);
        }

        $response = [
            'id' => $projectGroup->getId(),
            'name' => $projectGroup->getName(),
            'students' => array_map(function ($student) {
                return [
                    'id' => $student->getId(),
                    'name' => $student->getName(),
                    'surname' => $student->getSurname(),
                ];
            }, $projectGroup->getStudents()->toArray()),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}