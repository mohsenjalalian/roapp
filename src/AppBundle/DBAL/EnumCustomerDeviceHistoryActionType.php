<?php
namespace AppBundle\DBAL;


class EnumCustomerDeviceHistoryActionType extends EnumType
{
    const ENUM_CREATE = 1;
    const ENUM_VERIFY = 2;
    const ENUM_REPORT = 3;
    protected $name = 'enumCustomerDeviceHistoryAction';
    protected $values = [
        self::ENUM_CREATE,
        self::ENUM_VERIFY,
        self::ENUM_REPORT
    ];
}