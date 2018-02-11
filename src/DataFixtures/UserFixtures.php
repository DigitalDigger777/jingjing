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
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
        $this->loadConsumer($manager);
        $this->loadShopper($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadAdmin(ObjectManager $manager)
    {
        $user = new AdminUser();
        $user->setEmail('admin@test.com');
        $user->setPassword(md5('1demo!'));
        $user->setRole(null);
        $user->setToken(hash('sha256', '1demo!'));

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadConsumer(ObjectManager $manager)
    {
        $user = new ConsumerUser();
        $user->setEmail('consumer@test.com');
        $user->setPassword(md5('1demo!'));
        $user->setRole(null);
        $user->setToken(hash('sha256', '1demo!'));

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadShopper(ObjectManager $manager)
    {
        $user = new ShopperUser();
        $user->setEmail('shopper@test.com');
        $user->setPassword(md5('1demo!'));
        $user->setName('ABC Shopper');
        $user->setAddress('Test address');
        $user->setCell('+380991576192');
        $user->setContact('John Doe');
        $user->setRole(null);
        $user->setToken(hash('sha256', '1demo!'));

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 0;
    }
}