<?php

namespace App\Entity\Zone;

use App\Entity\Event\Event;
use App\Repository\Zone\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
#[ORM\Table(name: 'rc_zone')]
class Zone
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var int|null
     */
    #[ORM\Column]
    private ?int $postalCode = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $locality = null;
    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8)]
    private ?string $longitude = null;
    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8)]
    private ?string $latitude = null;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Event::class)]
    private Collection|ArrayCollection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->locality;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    /**
     * @param int $postalCode
     *
     * @return $this
     */
    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     *
     * @return $this
     */
    public function setLocality(string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     *
     * @return $this
     */
    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     *
     * @return $this
     */
    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEvent(): Collection
    {
        return $this->events;
    }

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setZone($this);
        }

        return $this;
    }

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getZone() === $this) {
                $event->setZone(null);
            }
        }

        return $this;
    }
}
