<?php
namespace AppBundle\DBAL;

/**
 * Class EnumPersonDeviceHistoryActionType
 * @package AppBundle\DBAL
 */
class EnumContractType extends EnumType
{
    const ENUM_PER_MONTH = 1;
    const ENUM_PER_SHIPMENT = 2;
    protected $name = 'enumContract';
    protected $values = [
        self::ENUM_PER_MONTH,
        self::ENUM_PER_SHIPMENT,
    ];
}
