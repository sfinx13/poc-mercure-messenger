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

class DeleteMessageHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    protected ParameterBagInterface $parameterBag;

    protected HubInterface $hub;

    protected AdapterInterface $cache;

    /**
     * DeleteMessageHandler constructor.
     * @param ParameterBagInterface $parameterBag
     * @param HubInterface $hub
     * @param AdapterInterface $cache
     */
    public function __construct(ParameterBagInterface $parameterBag,
                                HubInterface $hub,
                                AdapterInterface $cache)
    {
        $this->parameterBag = $parameterBag;
        $this->hub = $hub;
        $this->cache = $cache;
    }

    /**
     * @param DeleteMessage $deleteMessage
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __invoke(DeleteMessage $deleteMessage)
    {
        $appUrl = $this->parameterBag->get('app_url');

        if ($deleteMessage->getExtension() === 'csv') {
            $csvDir = $this->parameterBag->get('kernel.project_dir') . '/public/csv/';
            $filesystem = new Filesystem();
            if ($filesystem->exists($csvDir)) {
                $filesystem->remove($csvDir);

                $data = ['delete' => 'All files has been deleted'];
                $deleteUpdate = new Update($appUrl . '/delete', json_encode($data));
            } else {
                $data = ['delete' => 'No files to delete'];
                $deleteUpdate = new Update($appUrl . '/delete', json_encode($data));
            }
        } else {
            $data = ['delete' => 'Only csv can be deleted'];
            $deleteUpdate = new Update($appUrl . '/delete', json_encode($data));
        }

        $this->hub->publish($deleteUpdate);

        /** @var CacheItemInterface $counter */
        $counter = $this->cache->getItem('counter');
        $counter->set(0);
        $this->cache->save($counter);
        $data = ['counter' => (int)$counter->get()];
        $counterUpdate = new Update($appUrl . '/counter', json_encode($data));

        $this->hub->publish($counterUpdate);
    }
}
