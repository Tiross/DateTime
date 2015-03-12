<?php

namespace DateTime;

class DateTime extends \DateTime
{
    public function __construct($args = null, $tz = null)
    {
        $date = null;

        if (is_array($args)) {
            if (count($args)) {
                $year     = 0;
                $month    = 0;
                $day      = 0;
                $hour     = 0;
                $minute   = 0;
                $second   = 0;
                $timezone = 'UTC';

                extract($args, EXTR_OVERWRITE);

                $tmpDate = array($year, $month, $day);
                $tmpTime = array($hour, $minute, $second);

                $date = implode('-', $tmpDate) . 'T' . implode(':', $tmpTime);
            }
        } else {
            $date = $args;
        }

        if (is_string($tz)) {
            $tz = new TimeZone($tz);
        }

        parent::__construct($date, $tz);
    }
}
