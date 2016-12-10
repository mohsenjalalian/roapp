<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 */
class Permissions
{
    /**
     * @Annotation\Required
     *
     * @var array
     */
    public $permissions;

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
