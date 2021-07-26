<?php

namespace App\Command;

use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class SendNotificationCommand extends Command
{
    protected static $defaultName = 'app:send-notif';
    protected static $defaultDescription = 'Publish a notification';

    protected HubInterface $hub;
    protected ParameterBagInterface $parameterBag;

    public function __construct(
        HubInterface $hub,
        ParameterBagInterface $parameterBag,
        string $name = null
    ) {
        parent::__construct($name);
        $this->hub = $hub;
        $this->parameterBag = $parameterBag;
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
            $messageUpdate = new Update(
                $this->parameterBag->get('topic_url') . '/message',
                json_encode(['message' => $faker->text()]),
                false,
            );
            $this->hub->publish($messageUpdate);
        }

        $io->success('Message(s) has/have been published to the hub');

        return Command::SUCCESS;
    }
}
