<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\MessageDTO;
use App\Entity\Message;
use Psr\Log\LoggerInterface;

class MessageMapperService
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Maps an array of message entities to an array of MessageDTOs.
     *
     * @param Message[] $messages Array of message entities
     * @return array<MessageDTO>|null Array of MessageDTO objects
     */
    public function mapMessagesToDTOs(array $messages): ?array
    {
        if (empty($messages)) return null;
        
// Maps each message object to a MessageDTO.
// This makes the code more maintainable and testable.
        $messageDTOs = [];
        foreach ($messages as $message) {
            try {
                 $messageDTOs[] = MessageDTO::create(
                    $message->getUuid(),
                    $message->getText(),
                    $message->getStatus()
                );
                
            } catch (\InvalidArgumentException $e) {
                $this->logger->error('Failed to create MessageDTO: ' . $e->getMessage());
                continue;
            }
        }
        
        if (!$messageDTOs) return null;
        
        return $messageDTOs;
    }
    
    /**
     * Serializes MessageDTO[] to array of arrays.
     *
     * @param Message[] $messages Array of message entities
     * @return array<array<string, string|null>>|null Array of MessageDTO objects in arrays
     */
    public function serializeDTOsToArrays(array $messages): ?array {
        $messageDTOs = $this->mapMessagesToDTOs($messages);
        
        if (empty($messageDTOs)) return null;
        
        return array_map(
            fn ($messageDTO) => $messageDTO->toArray(),
            $messageDTOs
        );
    }
}
