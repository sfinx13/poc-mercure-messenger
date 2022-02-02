<?php

namespace App\Service\Notification;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Uid\Uuid;

class Notifier implements NotifierInterface
{
    public function __construct(private HubInterface $hub, private ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @throws \JsonException
     */
    public function send(Notification $notification): string
    {
        $topicUrl = $this->parameterBag->get('topic_url');
        $topics = array_map(static function ($topic) use ($topicUrl) {
            return $topicUrl . $topic;
        }, $notification->getTopics());

        $update = new Update(
            $topics,
            json_encode($notification->getData(), JSON_THROW_ON_ERROR),
            $notification->isPrivate(),
            Uuid::v4(),
            $notification->getEventType()
        );

        return $this->hub->publish($update);
    }
}
