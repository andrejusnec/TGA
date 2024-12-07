<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ProjectGroupApiController extends AbstractController
{
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

}