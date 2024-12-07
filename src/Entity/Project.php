<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project implements ProjectInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $amountOfGroups = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $maxStudentsPerGroup = null;

    #[ORM\OneToMany(targetEntity: ProjectGroup::class, mappedBy: 'project', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $projectGroups;

    public function __construct()
    {
        $this->projectGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAmountOfGroups(): ?int
    {
        return $this->amountOfGroups;
    }

    public function setAmountOfGroups(int $amountOfGroups): static
    {
        $this->amountOfGroups = $amountOfGroups;

        return $this;
    }

    public function getMaxStudentsPerGroup(): ?int
    {
        return $this->maxStudentsPerGroup;
    }

    public function setMaxStudentsPerGroup(int $maxStudentsPerGroup): static
    {
        $this->maxStudentsPerGroup = $maxStudentsPerGroup;

        return $this;
    }

    public function getGroups(): Collection
    {
        return $this->projectGroups;
    }

    public function addGroup(ProjectGroup $group): static
    {
        if (!$this->projectGroups->contains($group)) {
            $this->projectGroups->add($group);
            $group->setProject($this);
        }

        return $this;
    }

    public function removeGroup(ProjectGroup $group): static
    {
        if ($this->projectGroups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getProject() === $this) {
                $group->setProject(null);
            }
        }

        return $this;
    }
}
