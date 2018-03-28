<?php
/**
 * Created by PhpStorm.
 * User: korman
 * Date: 09.02.18
 * Time: 16:30
 */

namespace App\DataFixtures;


use App\Entity\AdminUser;
use App\Entity\ConsumerUser;
use App\Entity\Device;
use App\Entity\ShopperUser;
use App\Entity\Statement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 30; $i++) {
            $this->loadDevice($manager);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadDevice(ObjectManager $manager)
    {
        $shopper = $this->getRandomShopper($manager);

        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . rand(0, 3) . 'D'));

        $device = new Device();
        $device->setShopper($shopper);
        $device->setIsEnable(true);
        $device->setMac('EC:FA:BC:'. rand(0, 9) .'5:'. rand(0, 9) .'C:'. rand(0, 9) .'F');
        $device->setName('Purifier #' . rand(1000,9999));
        $device->setRoom('Room ' . rand(1, 50));
        $device->setShopperId($shopper->getId());
        $device->setStatus(rand(0, 2));
        $device->setDate($date);
        $manager->persist($device);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    private function getRandomConsumer(ObjectManager $manager)
    {
        $consumers = $manager->getRepository(ConsumerUser::class)->findAll();
        shuffle($consumers);
        return $consumers[0];
    }

    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    private function getRandomShopper(ObjectManager $manager)
    {
        $shoppers = $manager->getRepository(ShopperUser::class)->findAll();
        shuffle($shoppers);
        return $shoppers[0];
    }

    public function getOrder()
    {
        return 2;
    }
}