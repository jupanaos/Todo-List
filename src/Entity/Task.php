<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    #[NotBlank(message: 'Vous devez saisir un titre.')]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[NotBlank(message: 'Vous devez saisir du contenu.')]
    private ?string $content = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isDone = false;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->isDone = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(Datetime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function toggle(bool $flag): self
    {
        $this->isDone = $flag;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
