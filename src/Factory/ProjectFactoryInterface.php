<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProjectInterface;

interface ProjectFactoryInterface
{
    public function create(): ProjectInterface;
}