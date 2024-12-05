<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface ProjectInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    public function setTitle(string $title): static;

    public function getAmountOfGroups(): ?int;

    public function setAmountOfGroups(int $amountOfGroups): static;

    public function getMaxStudentsPerGroup(): ?int;

    public function setMaxStudentsPerGroup(int $maxStudentsPerGroup): static;

    public function getGroups(): Collection;

    public function addGroup(ProjectGroup $group): static;

    public function removeGroup(ProjectGroup $group): static;
}