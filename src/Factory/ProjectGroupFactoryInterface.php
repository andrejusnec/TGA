<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProjectGroup;
use App\Entity\ProjectInterface;

interface ProjectGroupFactoryInterface
{
    public function createProjectGroupsForProject(ProjectInterface $project): array;
}