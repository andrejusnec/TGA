<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Project;
use App\Entity\ProjectInterface;

class ProjectFactory implements ProjectFactoryInterface
{
    public function create(): ProjectInterface
    {
        return new Project();
    }
}