<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
/**
 * TODO: Review Message class
 */
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private string $uuid;
    
// field is not defined if it can be nullable neither in DB ir in attribute
    #[ORM\Column(length: 255)]
    private string $text;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;
    
    public function __construct()
    {
// UUID shouldn't be changeable after creation. A method setUuid() is unnecessary.
// createdAt should be initialized on creation otherwise it won't be set until setCreatedAt()
        $this->uuid = Uuid::v6()->toRfc4122();
        $this->createdAt = new DateTime();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
}
