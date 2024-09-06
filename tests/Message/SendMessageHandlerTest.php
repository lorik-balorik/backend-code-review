<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SendMessageHandlerTest extends TestCase
{
    public function test_invoke(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Message $message) {
                return $message->getText() === 'Hello, World!' && $message->getStatus() === 'sent';
            }));
        
        $entityManager->expects($this->once())
            ->method('flush');
        
        $handler = new SendMessageHandler($entityManager);
        $sendMessage = new SendMessage('Hello, World!');
        $handler($sendMessage);
        
    }
}
