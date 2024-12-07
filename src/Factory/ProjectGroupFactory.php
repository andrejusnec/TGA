<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProjectGroup;
use App\Entity\ProjectInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProjectGroupFactory implements ProjectGroupFactoryInterface
{
    public const DEFAULT_GROUP_NAME = 'Group';
    public function __construct(public readonly EntityManagerInterface $entityManager)
    {
    }

    public function createProjectGroupsForProject(ProjectInterface $project): array
    {
        $groups = [];
        for ($i = 1; $i <= $project->getAmountOfGroups(); $i++) {
            $groups[] = $this->createProjectGroup($project, $i);
        }

        return $groups;
    }

    private function createProjectGroup(ProjectInterface $project, int $iteration): ProjectGroup
    {
        $group = new ProjectGroup();
        $group->setProject($project);
        $group->setName(self::DEFAULT_GROUP_NAME . ' #' . $iteration);

        return $group;
    }
}