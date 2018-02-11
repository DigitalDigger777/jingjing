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
use App\Entity\ShopperUser;
use App\Entity\Statement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class StatementFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 30; $i++) {
            $this->loadStatement($manager);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadStatement(ObjectManager $manager)
    {
        $consumer = $this->getRandomConsumer($manager);
        $shopper = $this->getRandomShopper($manager);

        $statement = new Statement();
        $statement->setAmount(rand(1, 34));
        $statement->setDate(new \DateTime());
        $statement->setHours(rand(1, 9));
        $statement->setRate(3);
        $statement->setRoom(rand(1, 20));
        $statement->setConsumer($consumer);
        $statement->setShopper($shopper);

        $manager->persist($statement);
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
        return 1;
    }
}