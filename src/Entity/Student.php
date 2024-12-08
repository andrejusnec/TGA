<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StudentRepository;
use App\Validator\UniqueNameConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[UniqueNameConstraint]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Name must be at least {{ limit }} characters long',
        maxMessage: 'Name value is too long. It should have {{ limit }} characters or less.',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Surname must be at least {{ limit }} characters long',
        maxMessage: 'Surname value is too long. It should have {{ limit }} characters or less.',
    )]
    private ?string $surname = null;

    #[ORM\ManyToOne(targetEntity: ProjectGroup::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?ProjectGroup $projectGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getProjectGroup(): ?ProjectGroup
    {
        return $this->projectGroup;
    }

    public function setProjectGroup(?ProjectGroup $projectGroup): static
    {
        $this->projectGroup = $projectGroup;

        return $this;
    }
}
