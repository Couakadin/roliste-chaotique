<?php

namespace App\Entity\Event;

use App\Entity\Table\Table;
use App\Repository\Event\EventColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventColorRepository::class)]
#[ORM\Table(name: 'rc_event_color')]
class EventColor
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var Table|null
     */
    #[ORM\ManyToOne(inversedBy: 'eventColors')]
    private ?Table $table = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 7)]
    private ?string $bgColor = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 7)]
    private ?string $borderColor = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Table|null
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    /**
     * @param Table|null $table
     *
     * @return $this
     */
    public function setTable(?Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    /**
     * @param string $bgColor
     *
     * @return $this
     */
    public function setBgColor(string $bgColor): self
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBorderColor(): ?string
    {
        return $this->borderColor;
    }

    /**
     * @param string $borderColor
     *
     * @return $this
     */
    public function setBorderColor(string $borderColor): self
    {
        $this->borderColor = $borderColor;

        return $this;
    }
}
