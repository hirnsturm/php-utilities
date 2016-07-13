<?php

namespace Sle\Utilities\DataType;

/**
 * DateUtility
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class DateUtility
{

    /**
     * Validate a date by given format
     *
     * @param string $date The date as string
     * @param string $format The format like 'Y-m-d', 'd.m.Y', 'd.m.y', etc.
     * @return boolean
     */
    public static function validateDateByFormat($date, $format)
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * Calculates the nights between two dates
     *
     * @param $date1
     * @param $date2
     * @return int
     */
    public static function nightsBetweenTwoDates($date1, $date2)
    {
        $dateFrom = new \DateTime($date1);
        $dateTo = new \DateTime($date2);
        $diff = $dateFrom->diff($dateTo);

        return $diff->format('%a');
    }

}
