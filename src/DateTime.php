<?php

namespace Tiross\DateTime;

class DateTime extends \DateTime
{
    /** @type bool */
    protected $isFinite = true;

    public function __construct($args = null, $tz = null)
    {
        $date = $args;

        if (is_array($args)) {
            if (count($args)) {
                $year     = 0;
                $month    = 0;
                $day      = 0;
                $hour     = 0;
                $minute   = 0;
                $second   = 0;
                $timezone = null;

                extract($args, EXTR_OVERWRITE);

                $tmpDate = array($year, $month, $day);
                $tmpTime = array($hour, $minute, $second);

                $date = implode('-', $tmpDate) . 'T' . implode(':', $tmpTime);

                $tz = $timezone;
            }
        }

        if (is_string($tz)) {
            $tz = new TimeZone($tz);
        } elseif ($tz instanceof \DateTimeZone) {
            $tz = TimeZone::convert($tz);
        }

        parent::__construct($date, $tz);
    }
}
