<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function test_find_all_returns_correct_data(): void
    {
        self::bootKernel();
        
        /** @var MessageRepository $messageRepository*/
        $messageRepository = self::getContainer()->get(MessageRepository::class);
        
        $result = $messageRepository->findAll();
        
        $this->assertInstanceOf(Message::class, $result[0]);
    }
}
