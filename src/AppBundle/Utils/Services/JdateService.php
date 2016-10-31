<?php
/**
 * Created by PhpStorm.
 * User: msm
 * Date: 10/31/16
 * Time: 12:44 PM
 */

namespace AppBundle\Utils\Services;

use jDateTime;
use DateTime;
class JdateService
{
    public function convertToGregorian($jDate)
    {
        $date = explode("-",substr($jDate,0,10));
        $time = substr($jDate,11,-3);
        $gregorianArr = jDateTime::toGregorian($date[0],$date[1],$date[2]);
        $gregorianDate = new DateTime(
            sprintf(
                "%s-%s-%s %s",
                $gregorianArr[0],
                $gregorianArr[1],
                $gregorianArr[2],
                $time
            )
        );

        return $gregorianDate;
        
    }
}