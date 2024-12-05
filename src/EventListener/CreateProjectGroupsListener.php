<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ProjectCreatedEvent;
use App\Factory\ProjectGroupFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class CreateProjectGroupsListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectGroupFactoryInterface $projectGroupFactory,
    ) {
    }

    public function onProjectCreate(ProjectCreatedEvent $event): void
    {
        $project = $event->getProject();

        $groups = $this->projectGroupFactory->createProjectGroupsForProject($project);

        foreach ($groups as $group) {
            $project->addGroup($group);
            $this->entityManager->persist($group);
        }

        $this->entityManager->flush();
    }
}
