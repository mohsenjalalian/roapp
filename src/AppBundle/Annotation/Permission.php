<?php

namespace AppBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Annotation
 * @Annotation\Target("ANNOTATION")
 */
class Permission
{
    /**
     * @Annotation\Required
     *
     * @var string
     */
    public $mappedConst;

    /**
     * @Annotation\Required
     * @Enum({"object", "class"})
     * @var string
     */
    public $type;

    /**
     * @Annotation\Required
     * @var array<string>
     */
    public $scope;

    /**
     * @Annotation\Required
     * @var string
     */
    public $label;

    /**
     * @return string
     */
    public function getMappedConst()
    {
        return $this->mappedConst;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array<string>
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
