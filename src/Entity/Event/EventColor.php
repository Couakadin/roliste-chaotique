<?php

namespace App\Entity\Event;

use App\Entity\Table\Table;
use App\Repository\Event\EventColorRepository;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventColorRepository::class)]
#[ORM\Table(name: 'rc_event_color')]
class EventColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventColors')]
    private ?Table $table = null;

    #[ORM\Column(length: 7)]
    #[Assert\Length(max: 7, maxMessage: 'entity.length.max')]
    private ?string $bgColor = null;

    #[ORM\Column(length: 7)]
    #[Assert\Length(max: 7, maxMessage: 'entity.length.max')]
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
