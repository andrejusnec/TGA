<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Factory\ProjectFactoryInterface;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\StudentRepositoryInterface;
use App\Service\ProjectServiceInterface;
use App\Service\StudentServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService
    ) {
    }

    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        if (count($projects) > 0) {
            return $this->render('project/index.html.twig', [
                'projects' => $projects,
            ]);
        }

        return $this->redirectToRoute('app_project_new');
    }


    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ProjectFactoryInterface $projectFactory,
    ): Response {
        $project = $projectFactory->create();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectService->save($project);
            $this->projectService->dispatchProjectCreationEvent($project);
            $this->addFlash('success', 'Project created successfully.');

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }


    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $this->projectService->delete($project);

        $this->addFlash('success', 'Project successfully deleted.');

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/status', name: 'app_project_status', methods: ['GET'])]
    public function status(
        Request $request,
        Project $project,
        StudentServiceInterface $studentService,
    ): Response {
        $page = max(1, $request->query->getInt('page', 1));
        $paginationData = $studentService->getPaginatedAndUnassignedStudents($page, 10);

        return $this->render('project/status.html.twig', [
            'currentPage' => $page,
            'project' => $project,
            'students' => $paginationData['paginatedStudents'] ?? null,
            'totalPages' => $paginationData['totalPages'] ?? null,
            'allStudents' => $paginationData['unassignedStudents'] ?? null,
        ]);
    }
}
