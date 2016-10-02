<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Driver
 * @ORM\Entity
 */
class Driver extends Person
{
    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return array('ROLE_DRIVER');
    }
}
