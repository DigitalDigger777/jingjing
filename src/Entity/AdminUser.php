<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminUserRepository")
 */
class AdminUser extends User
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
        parent::setRole('ROLE_ADMIN');
    }
}
