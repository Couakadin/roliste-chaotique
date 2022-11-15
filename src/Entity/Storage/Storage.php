<?php

namespace App\Entity\Storage;

use App\Entity\Folder\Folder;
use App\Entity\User\User;
use App\Repository\Storage\StorageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Table(name: 'rc_storage')]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['slug'], message: 'entity.unique')]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'storages', fileNameProperty: 'fileName', size: 'size', mimeType: 'type', originalName: 'originalName', dimensions: 'dimensions')]
    #[Assert\File(
        maxSize: '100M',
        mimeTypes: [
            'application/pdf',
            'application/x-pdf',
            'image/gif',
            'image/jpeg',
            'image/png',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        maxSizeMessage: 'entity.file.size',
        mimeTypesMessage: 'entity.file.type'
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?string $fileName = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['fileName'])]
    #[Assert\Length(max: 128, maxMessage: 'entity.length.max')]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?int $size = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?string $originalName = null;

    #[ORM\Column(type: Types::JSON)]
    private ?array $dimensions = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'storages')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'storages')]
    private ?Folder $folder = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $imageFile
     *
     * @return void
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     *
     * @return Storage
     */
    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return Storage
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return Storage
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     *
     * @return Storage
     */
    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * @param string|null $originalName
     *
     * @return Storage
     */
    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * @return array
     */
    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    /**
     * @param array|null $dimensions
     *
     * @return $this
     */
    public function setDimensions(?array $dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable|null $createdAt
     *
     * @return Storage
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable|null $updatedAt
     *
     * @return Storage
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return Storage
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Folder|null
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    /**
     * @param Folder|null $folder
     *
     * @return $this
     */
    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }
}
