<?php

declare(strict_types=1);

namespace App\DTO;

class MessageDTO
{
    private string $uuid;
    private string $text;
    private ?string $status;

// Private constructor to enforce the use of factory methods
    private function __construct(string $uuid, string $text, ?string $status)
    {
        $this->uuid = $uuid;
        $this->text = $text;
        $this->status = $status;
    }
    
// Factory method for creating a new MessageDTO with validation
    public static function create(string $uuid, string $text, ?string $status): self
    {
// Validate UUID
        if (!self::isValidUuid($uuid)) {
            throw new \InvalidArgumentException('Invalid UUID format.');
        }
        
// Validate text (non-empty and within reasonable length)
        if (empty($text) || strlen($text) > 250) {
            throw new \InvalidArgumentException('Text must be non-empty and no more than 250 characters.');
        }
        
// Validate status against allowed values
        if (!in_array($status, ['read', 'sent'], true)) {
            throw new \InvalidArgumentException('Invalid status value.');
        }
        
        return new self($uuid, $text, $status);
    }
    
    public function getUuid(): string
    {
        return $this->uuid;
    }
    
    public function getText(): string
    {
        return $this->text;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    /**
    * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'text' => $this->getText(),
            'status' => $this->getStatus(),
        ];
    }
    
// validate UUID format
    private static function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid) === 1;
    }
}
