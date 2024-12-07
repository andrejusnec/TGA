<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ProjectInterface;

interface ProjectServiceInterface
{
    public function save(ProjectInterface $project): void;

    public function delete(ProjectInterface $project): void;

    public function dispatchProjectCreationEvent(ProjectInterface $project): void;
}