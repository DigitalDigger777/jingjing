<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $mac;

    /**
     * @ORM\Column(type="integer", name="shopper_id", nullable=true)
     */
    private $shopperId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnable = false;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $room;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAdd;

    /**
     * @ORM\ManyToOne(targetEntity="ShopperUser", inversedBy="devices")
     * @ORM\JoinColumn(name="shopper_id", referencedColumnName="id")
     */
    private $shopper;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @param mixed $mac
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
    }

    /**
     * @return mixed
     */
    public function getShopperId()
    {
        return $this->shopperId;
    }

    /**
     * @param mixed $shopperId
     */
    public function setShopperId($shopperId)
    {
        $this->shopperId = $shopperId;
    }

    /**
     * @return mixed
     */
    public function getIsEnable()
    {
        return $this->isEnable;
    }

    /**
     * @param mixed $isEnable
     */
    public function setIsEnable($isEnable)
    {
        $this->isEnable = $isEnable;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param mixed $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return mixed
     */
    public function getShopper()
    {
        return $this->shopper;
    }

    /**
     * @param mixed $shopper
     */
    public function setShopper($shopper)
    {
        $this->shopper = $shopper;
    }

    /**
     * @return mixed
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * @param mixed $dateAdd
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }
}
