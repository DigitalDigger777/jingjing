<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopperUserRepository")
 */
class TesterUser extends User
{

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $cell;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $pin;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_deleted")
     */
    private $isDeleted;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * @param mixed $cell
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        parent::setRole('ROLE_TESTER');
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }
}
