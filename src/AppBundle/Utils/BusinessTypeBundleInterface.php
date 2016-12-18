<?php

namespace AppBundle\Utils;

/**
 * Interface BusinessTypeInterface
 * @package AppBundle\Utils
 */
interface BusinessTypeBundleInterface
{
    /**
     * @return string
     */
    public static function getShipmentFormNamespace();

    /**
     * @return string
     */
    public static function getShipmentEntityNamespace();

    /**
     * @return string
     */
    public static function getBusinessTypeName();
}
