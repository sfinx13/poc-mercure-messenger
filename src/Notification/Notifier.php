<?php

namespace App\Notification;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Uid\Uuid;

class Notifier
{
    private HubInterface $hub;

    private ParameterBagInterface $parameterBag;

    public function __construct(HubInterface $hub, ParameterBagInterface $parameterBag)
    {
        $this->hub = $hub;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws \JsonException
     */
    public function send(Notification $notification): void
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

        $this->hub->publish($update);
    }
}
