<?php
/**
 * Created by PhpStorm.
 * User: amir
 * Date: 12/15/16
 * Time: 9:28 PM
 */

namespace AppBundle\DBAL;

/**
 * Interface EnumInterface
 * @package AppBundle\DBAL
 */
interface EnumInterface
{
    /**
     * @return string[]
     */
    public static function getValues();
}
