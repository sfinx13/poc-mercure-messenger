<?php

namespace App\DataFixtures;

use App\Entity\NotificationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class NotificationTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dummyData = Yaml::parseFile(__DIR__.'/Data/notification-type.yaml');

        foreach ($dummyData['notification-types'] as  $dummyNotificationType) {
            $notificationType = (new NotificationType())
                ->setName($dummyNotificationType['name'])
                ->setType($dummyNotificationType['type'])
                ->setTemplate($dummyNotificationType['template'])
                ->setSlug(str_replace(' ', '-', strtolower($dummyNotificationType['name'])));

            $manager->persist($notificationType);
        }
        $manager->flush();
    }
}
