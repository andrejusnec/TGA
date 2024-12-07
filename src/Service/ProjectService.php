<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ProjectInterface;
use App\Event\ProjectCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function save(ProjectInterface $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function delete(ProjectInterface $project): void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    public function dispatchProjectCreationEvent(ProjectInterface $project): void
    {
        $this->eventDispatcher->dispatch(new ProjectCreatedEvent($project));
    }
}
