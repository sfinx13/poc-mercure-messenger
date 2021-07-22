<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class SendNotificationCommand extends Command
{
    protected static $defaultName = 'app:send-notification';
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
            ->addArgument('message', InputArgument::REQUIRED, 'Message to send to application');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');

        if ($message) {
            $io->note(sprintf('You passed an argument: %s', $message));
        }

        $messageUpdate = new Update(
            $this->parameterBag->get('topic_url') . '/message',
            json_encode(['message' => $message])
        );

        $this->hub->publish($messageUpdate);

        $io->success('Message has been published to the hub');

        return Command::SUCCESS;
    }
}
