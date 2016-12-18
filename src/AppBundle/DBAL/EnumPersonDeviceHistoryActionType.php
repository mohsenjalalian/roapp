<?php
namespace AppBundle\DBAL;

/**
 * Class EnumPersonDeviceHistoryActionType
 * @package AppBundle\DBAL
 */
class EnumPersonDeviceHistoryActionType extends EnumType
{
    const ENUM_CREATE = 1;
    const ENUM_VERIFY = 2;
    const ENUM_REPORT = 3;
    protected $name = 'enumPersonDeviceHistoryAction';

    /**
     * @return string[]
     */
    public static function getValues()
    {
        return [
            self::ENUM_CREATE => 'Create',
            self::ENUM_VERIFY => 'Verify',
            self::ENUM_REPORT => 'Report',
        ];
    }
}
