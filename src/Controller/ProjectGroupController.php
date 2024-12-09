<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/group')]
final class ProjectGroupController extends AbstractController
{
    public function __construct(
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
}