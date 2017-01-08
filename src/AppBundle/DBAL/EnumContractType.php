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

    /**
     * @return string[]
     */
    public static function getValues()
    {
        return [
            self::ENUM_PER_MONTH => 'ماهیانه',
            self::ENUM_PER_SHIPMENT => 'به ازای هر مرسوله',
        ];
    }
}
