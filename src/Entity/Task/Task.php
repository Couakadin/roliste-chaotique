<?php

namespace App\Entity\Task;

use App\Repository\Task\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'rc_task')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?string $todo = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTodo(): ?string
    {
        return $this->todo;
    }

    /**
     * @param string $todo
     *
     * @return $this
     */
    public function setTodo(string $todo): self
    {
        $this->todo = $todo;

        return $this;
    }
}
