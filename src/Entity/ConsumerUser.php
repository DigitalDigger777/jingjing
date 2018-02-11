<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsumerUserRepository")
 */
class ConsumerUser extends User
{
    /**
     * @ORM\OneToMany(targetEntity="Statement", mappedBy="consumer")
     */
    private $statements;

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        parent::setRole('ROLE_CONSUMER');
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
}
