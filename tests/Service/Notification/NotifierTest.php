<?php

namespace App\Tests\Service\Notification;

use App\Service\Notification\Notification;
use App\Service\Notification\Notifier;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\MockHub;
use Symfony\Component\Mercure\Update;

class NotifierTest extends KernelTestCase
{
    private Notifier $notifier;
    private HubInterface $hub;
    private ?ParameterBagInterface $parameterBag;

    public function setUp(): void
    {
        $this->hub = new MockHub(
            'https://internal/.well-known/mercure',
            new StaticTokenProvider('foo'),
            function (Update $update): string {
                $this->assertTrue($update->isPrivate());
                $this->assertEquals(
                    ['https://example.com/user/demo-1/files'],
                    $update->getTopics()
                );

                return 'urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a';
            }
        );


        $this->parameterBag = static::getContainer()->get(ParameterBagInterface::class);
        $this->notifier = new Notifier($this->hub, $this->parameterBag);
    }

    public function testSendSuccess(): void
    {
        $notificationMessage = new Notification(
            ['user/demo-1/files'],
            ['message' => 'hello world']
        );

        $response = (new Notifier($this->hub, $this->parameterBag))->send($notificationMessage);

        $this->assertEquals('urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a', $response);
    }

    private function getData(): string
    {
        return <<<JSON
            {"message":"hello world"}
            JSON;
    }
}
