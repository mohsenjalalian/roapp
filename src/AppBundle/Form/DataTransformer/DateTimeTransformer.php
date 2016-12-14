<?php

namespace AppBundle\Form\DataTransformer;

use DateTime;
use jDateTime;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimeTransformer
 * @package AppBundle\Form\DataTransformer
 */
class DateTimeTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $gDate
     * @return mixed
     * @inheritdoc
     */
    public function transform($gDate)
    {
        if (empty($gDate)) {
            return $gDate;
        }

        return $gDate->format("Y-m-d H:i:s");
    }

    /**
     * @param mixed $jDate
     * @return DateTime
     * @inheritdoc
     */
    public function reverseTransform($jDate)
    {
        if (empty($jDate)) {
            return null;
        }

        $date = explode("-", substr($jDate, 0, 10));
        $time = substr($jDate, 11, -3);
        $gregorianArr = jDateTime::toGregorian($date[0], $date[1], $date[2]);
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
