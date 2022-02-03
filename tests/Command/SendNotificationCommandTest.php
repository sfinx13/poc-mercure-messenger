<?php

namespace App\Tests\App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SendNotificationCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();

        $command = (new Application(self::$kernel))->find('app:send-notif');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $this->assertTrue($commandTester->getInput()->getOption('iterations') >= 1);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Message(s) has/have been published to the hub', $output);
    }
}
