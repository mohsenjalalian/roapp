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

    /**
     * @return string
     */
    public static function getBusinessUnitFormNamespace();

    /**
     * @return string
     */
    public static function getBusinessUnitEntityNamespace();

    /**
     * @return string
     */
    public static function getBusinessTypeSingleShipmentTitle();

    /**
     * @return string
     */
    public static function getBusinessTypePluralShipmentTitle();

    /**
     * @return string
     */
    public static function getBusinessTypePersianName();
}
