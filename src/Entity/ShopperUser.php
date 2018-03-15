<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopperUserRepository")
 */
class ShopperUser extends User
{

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $cell;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_deleted")
     */
    private $isDeleted;

    /**
     * @ORM\OneToMany(targetEntity="Statement", mappedBy="shopper")
     */
    private $statements;

    /**
     * @ORM\OneToMany(targetEntity="Device", mappedBy="shopper")
     */
    private $devices;

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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param mixed $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
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
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        parent::setRole('ROLE_SHOPPER');
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

    /**
     * @return mixed
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * @param mixed $statements
     */
    public function setStatements($statements)
    {
        $this->statements = $statements;
    }

    /**
     * @return mixed
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @param mixed $devices
     */
    public function setDevices($devices)
    {
        $this->devices = $devices;
    }
}
