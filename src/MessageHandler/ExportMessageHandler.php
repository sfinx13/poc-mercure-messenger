<?php

namespace App\MessageHandler;

use App\Message\ExportMessage;
use App\Utils\MathHelper;
use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ExportMessageHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    protected ParameterBagInterface $parameterBag;

    protected HubInterface $hub;

    protected AdapterInterface $cache;

    public function __construct(ParameterBagInterface $parameterBag,
                                HubInterface $hub,
                                AdapterInterface $cache)
    {
        $this->parameterBag = $parameterBag;
        $this->hub = $hub;
        $this->cache = $cache;
    }

    public function __invoke(ExportMessage $exportMessage)
    {
        $filesystem = new Filesystem();
        $appUrl = $this->parameterBag->get('app_url');
        $csvDir = $this->parameterBag->get('kernel.project_dir') . '/public/csv/';
        $filename = 'export_' . $exportMessage->getProjectId() . '_' . $exportMessage->getStartDate()->getTimestamp() . '.csv';
        $csvFile = $csvDir . $filename;

        if (!$filesystem->exists($csvDir)) {
            $filesystem->mkdir($csvDir);
        }

        $filesystem->touch($csvFile);
        $stopDate = (new DateTime())
            ->add(date_interval_create_from_date_string($exportMessage->getInterval() . ' seconds'));
        $start = new DateTime();

        while (new DateTime() < $stopDate) {
            $now = DateTime::createFromFormat('U.u', microtime(true));

            if ($now !== false) {
                $line = $now->format("Y-m-d H:i:s.u") . ';' . MathHelper::randomFloat(0, 1);
                $filesystem->appendToFile($csvFile, $line . PHP_EOL);
                $percentage = (($stopDate->getTimestamp() - (new DateTime())->getTimestamp()) * 100) / ($stopDate->getTimestamp() - $start->getTimestamp());
                $data = ['percentage' => abs($percentage - 100)];

                $percentageUpdate = new Update($appUrl . '/percentage', json_encode($data));
                $this->hub->publish($percentageUpdate);
            }
        }

        $data = [
            'timestamp' => time(),
            'filename' => $filename,
            'size' => round((new File($csvFile))->getSize() / 1024) . ' Ko'];

        $notificationUpdate = new Update($appUrl . '/notification', json_encode($data));
        $this->hub->publish($notificationUpdate);

        /** @var CacheItemInterface $counter */
        $counter = $this->cache->getItem('counter');
        if ($counter->isHit() && $counter->get() > 0) {
            $counter->set((int)$counter->get() - 1);
            $this->cache->save($counter);
        }

        $data = ['counter' => (int)$counter->get()];

        $counterUpdate = new Update($appUrl . '/counter', json_encode($data));
        $this->hub->publish($counterUpdate);
    }

}
