<?php

namespace App\Tests;

use App\Manager\NotificationManager;
use App\Service\Notification\Notifier;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SmokeTest extends WebTestCase
{
    private $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url, $statusCode)
    {
        $notifierMock = $this->createMock(Notifier::class);
        $notificationManagerMock = $this->createMock(NotificationManager::class);

        self::$kernel->getContainer()->set(Notifier::class, $notifierMock);
        self::$kernel->getContainer()->set(NotificationManager::class, $notificationManagerMock);

        $this->client->request('GET', $url);

        echo $this->client->getResponse()->getContent();

        $this->assertTrue(
            $this->client->getResponse()->getStatusCode() === $statusCode,
            sprintf('Result value: %d', $this->client->getResponse()->getStatusCode())
        );
    }

    public function urlProvider()
    {
        return [
            'Homepage Redirection' => ['/', Response::HTTP_FOUND],
            'Login page' => ['/login', Response::HTTP_OK]
        ];
    }
}
