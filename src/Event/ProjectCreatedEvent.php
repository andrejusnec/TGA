<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\ProjectInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ProjectCreatedEvent extends Event
{
    public function __construct(private readonly ProjectInterface $project)
    {
    }

    public function getProject(): ProjectInterface
    {
        return $this->project;
    }
}
