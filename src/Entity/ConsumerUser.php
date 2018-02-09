<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsumerUserRepository")
 */
class ConsumerUser extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        parent::setRole('ROLE_CONSUMER');
    }
}
