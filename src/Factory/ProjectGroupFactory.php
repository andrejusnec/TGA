<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProjectGroup;
use App\Entity\ProjectInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProjectGroupFactory implements ProjectGroupFactoryInterface
{
    public function __construct(public readonly EntityManagerInterface $entityManager)
    {
    }

    public function createProjectGroupsForProject(ProjectInterface $project): array
    {
        $groups = [];
        for ($i = 0; $i < $project->getAmountOfGroups(); $i++) {
            $groups[] = $this->createProjectGroup($project);
        }

        return $groups;
    }

    public function createProjectGroup(ProjectInterface $project): ProjectGroup
    {
        $group = new ProjectGroup();
        $group->setProject($project);

        return $group;
    }
}