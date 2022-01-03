<?php

namespace App\Command;

use App\Service\Notification\Notification;
use App\Service\Notification\Notifier;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendNotificationCommand extends Command
{
    protected static $defaultName = 'app:send-notif';
    protected static $defaultDescription = 'Publish a notification';

    public function __construct(
        private Notifier $notifier,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption(
                'iterations',
                'i',
                InputOption::VALUE_REQUIRED,
                'How many times should be published',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $faker = Factory::create();

        for ($i = 0; $i < $input->getOption('iterations'); $i++) {
            $this->notifier->send(new Notification(['message'], ['message' => $faker->text()], true));
        }

        $io->success('Message(s) has/have been published to the hub');

        return Command::SUCCESS;
    }
}
