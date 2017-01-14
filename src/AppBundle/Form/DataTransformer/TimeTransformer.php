<?php

namespace AppBundle\Form\DataTransformer;

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

        return $gDate->format("Y-m-d H:i:s");
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
