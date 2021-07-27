<?php

namespace App\MessageHandler;

use App\Message\DeleteMessage;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\Uuid;

class DeleteMessageHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    protected ParameterBagInterface $parameterBag;
    protected HubInterface $hub;
    protected AdapterInterface $cache;

    public function __construct(
        ParameterBagInterface $parameterBag,
        HubInterface $hub,
        AdapterInterface $cache
    ) {
        $this->parameterBag = $parameterBag;
        $this->hub = $hub;
        $this->cache = $cache;
    }

    public function __invoke(DeleteMessage $deleteMessage)
    {
        $topicUrl = $this->parameterBag->get('topic_url'). '/files';
        if ($deleteMessage->getExtension() === 'csv') {
            $csvDir = $this->parameterBag->get('kernel.project_dir') . '/public/csv/';
            $filesystem = new Filesystem();
            if ($filesystem->exists($csvDir)) {
                $filesystem->remove($csvDir);
                $data['message'] = 'All files has been deleted';
            } else {
                $data['message'] = 'No files to delete';
            }
        } else {
            $data['message'] = 'Only csv can be deleted';
        }

        $deleteUpdate = new Update($topicUrl, json_encode($data), false, Uuid::v4(), 'delete-files');
        $this->hub->publish($deleteUpdate);

        /** @var CacheItemInterface $counter */
        $counter = $this->cache->getItem('counter');
        $counter->set(0);
        $this->cache->save($counter);

        $data = ['counter' => (int)$counter->get()];
        $counterUpdate = new Update($topicUrl, json_encode($data), false, Uuid::v4(), 'counter');
        $this->hub->publish($counterUpdate);
    }
}
