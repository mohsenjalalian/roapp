<?php

namespace AppBundle\Form\DataTransformer;

use r\Queries\Dates\Hours;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class DateTimeTransformer
 * @package AppBundle\Form\DataTransformer
 */
class TimeTransformer implements DataTransformerInterface
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
        $now = new \DateTime();
        $nowTimeStamp = $now->getTimestamp();
        $pickUpTime = $gDate->getTimeStamp();
        $realTime = $pickUpTime - $nowTimeStamp;
        if ($realTime > 0) {
            $interval = $gDate->diff($now);
            foreach ($interval as $key => $value) {
                if ($key == 'h' || $key == 'i') {
                    $time[$key] = $value;
                }
            }
            $min = ($time['h'] * 60) + $time['i'];
            $hour = $min / 60;
            $roundHour = floor($hour);
            if ($roundHour > 0 && $roundHour <= 12) {
                $mins = intval(($roundHour * 60));
            } else {
                $mins = 30;
            }

            return $mins;
        } else {
            return 30;
        }
    }

    /**
     * @param mixed $time
     * @return DateTime
     * @inheritdoc
     */
    public function reverseTransform($time)
    {
        if (empty($time)) {
            return null;
        }
        $now = new \DateTime();
        /** @var DateTime $gregorianDate */
        $gregorianDate = $now->add(new \DateInterval('PT'.$time.'M'));

        return $gregorianDate;
    }
}
