<?php
namespace AppBundle\DBAL;


class EnumPersonDeviceHistoryActionType extends EnumType
{
    const ENUM_CREATE = 1;
    const ENUM_VERIFY = 2;
    const ENUM_REPORT = 3;
    protected $name = 'enumPersonDeviceHistoryAction';
    protected $values = [
        self::ENUM_CREATE,
        self::ENUM_VERIFY,
        self::ENUM_REPORT
    ];
}