<?php

namespace App\MessageHandler;

use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Message\DeleteMessage;
use App\Service\Counter;
use App\Service\Publisher;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteMessageHandler implements MessageHandlerInterface
{
    private ParameterBagInterface $parameterBag;
    private Counter $counter;
    private FileManager $fileManager;
    private Publisher $publisher;
    private NotificationManager $notificationManager;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Publisher $publisher,
        Counter $counter,
        FileManager $fileManager,
        NotificationManager $notificationManager
    ) {
        $this->parameterBag = $parameterBag;
        $this->publisher = $publisher;
        $this->counter = $counter;
        $this->fileManager = $fileManager;
        $this->notificationManager = $notificationManager;
    }

    public function __invoke(DeleteMessage $deleteMessage)
    {
        if (!empty($deleteMessage->getFilename())) {
            $this->fileManager->removeFromFilename($deleteMessage->getFilename());
            $this->notificationManager->createNotification($deleteMessage, 'delete-file');
        }

        $this->fileManager->removeFromUser($deleteMessage->getUser());
        $username = $deleteMessage->getUser()->getUserIdentifier();

        $data['message'] = 'All files has been deleted';
        $topic = $this->parameterBag->get('topic_url') . '/files/' . $username;
        $this->publisher->publish($topic, $data, true, 'delete-files');

        $data = ['counter' => $this->counter->reset($username)];
        $this->publisher->publish($topic, $data, true, 'delete-files');

        $this->fileManager->removeFromUser($deleteMessage->getUser());
    }
}
