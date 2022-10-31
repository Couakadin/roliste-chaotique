<?php

namespace App\Entity\Task;

use App\Repository\Task\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'rc_task')]
class Task
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
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
