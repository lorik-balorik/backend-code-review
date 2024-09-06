<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;
    
    function test_list(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages', [
            'status' => 'sent',
            'order_by[created_at]' => 'desc',
            'limit' => 2,
            'offset' => 0,
        ]);
        
        $this->assertResponseIsSuccessful();
    }
    
    function test_that_it_sends_a_message(): void
    {
        $client = static::createClient();
        $client->request('POST', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}
