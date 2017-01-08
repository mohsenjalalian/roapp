<?php

namespace AppBundle\Twig;

/**
 * Class DateExtension
 * @package AppBundle\Twig
 */
class DateExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jdate', array($this, 'toJ')),
        );
    }

    /**
     * @param \DateTime $date
     * @param string    $format
     * @return string
     */
    public function toJ(\DateTime $date, $format = 'l مورخ j F Y')
    {

        $jdate = new \jDateTime(true, true, 'Asia/Tehran');
        $j = $jdate->toJalali($date->format('Y'), $date->format('n'), $date->format('j'));
        $jdate->mktime(0, 0, 0, $j[1], $j[2], $j[0]);

        return $jdate->date($format, $date->getTimestamp());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'date_extension';
    }
}
