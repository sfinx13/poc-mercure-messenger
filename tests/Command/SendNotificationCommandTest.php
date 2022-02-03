<?php

namespace App\Tests\App\Tests\Command;

use App\Service\Notification\Notifier;
use App\Service\Notification\NotifierInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SendNotificationCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();

        $notifierMock = $this->getMockBuilder(NotifierInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->getMock();

        $notifierMock->expects($this->once())
            ->method('send')
            ->willReturn('urn:uuid:7b26df22-bbc5-466f-b517-05aaa3b12f4a');

        self::$kernel->getContainer()->set(Notifier::class, $notifierMock);

        $command = (new Application(self::$kernel))->find('app:send-notif');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $this->assertTrue($commandTester->getInput()->getOption('iterations') >= 1);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Message(s) has/have been published to the hub', $output);
    }
}
