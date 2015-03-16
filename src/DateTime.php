<?php

/**
 * Better dates and times
 *
 * @author Tiross
 */
namespace Tiross\DateTime;

class DateTime extends \DateTime
{
    /** @type bool */
    protected $isFinite = true;

    public function __construct($args = null, $zone = null)
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

                $zone = $timezone ?: $zone;
            }
        }

        if (is_string($zone)) {
            $zone = new TimeZone($zone);
        } elseif ($zone instanceof \DateTimeZone) {
            $zone = TimeZone::convert($zone);
        }

        parent::__construct($date, $zone);
    }

    public static function now()
    {
        return new static;
    }

    public function cloneObject()
    {
        return clone $this;
    }

    public function __call($method, $arguments)
    {
        switch (strtolower($method)) {
            case 'clone':
                return $this->cloneObject();
        }

        $message = sprintf('Call to undefined method %s::%s()', __CLASS__, $method);
        throw new Exception\LogicException($message, 199);
    }

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'clone':

            case 'year':
            case 'month':
            case 'day':
            case 'hour':
            case 'minute':
            case 'second':

            case 'ymd':
            case 'dmy':
            case 'hms':
                return $this->$property();
        }

        $message = sprintf('Undefined property: %s::$%s', __CLASS__, $property);
        throw new Exception\LogicException($message, 198);
    }

    /**
     * Returns or modifies current year
     *
     * If no argument is passed, the method simply returns the current year.
     *
     * If a argument is given, this will be used as new year and the year before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function year($value = null)
    {
        return $this->unitSetter('Y', $value);
    }

    /**
     * Returns or modifies current month
     *
     * If no argument is passed, the method simply returns the current month.
     *
     * If a argument is given, this will be used as new month and the month before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function month($value = null)
    {
        return $this->unitSetter('m', $value);
    }

    /**
     * Returns or modifies current day
     *
     * If no argument is passed, the method simply returns the current day.
     *
     * If a argument is given, this will be used as new day and the day before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function day($value = null)
    {
        return $this->unitSetter('d', $value);
    }

    /**
     * Returns or modifies current hour
     *
     * If no argument is passed, the method simply returns the current hour.
     *
     * If a argument is given, this will be used as new hour and the hour before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function hour($value = null)
    {
        return $this->unitSetter('H', $value);
    }

    /**
     * Returns or modifies current minute
     *
     * If no argument is passed, the method simply returns the current minute.
     *
     * If a argument is given, this will be used as new minute and the minute before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function minute($value = null)
    {
        return $this->unitSetter('i', $value);
    }

    /**
     * Returns or modifies current second
     *
     * If no argument is passed, the method simply returns the current second.
     *
     * If a argument is given, this will be used as new second and the second before the change will be return
     * by the method.
     *
     * @param  integer|null $value
     * @return integer
     */
    public function second($value = null)
    {
        return $this->unitSetter('s', $value);
    }

    protected function unitSetter($pattern, $value = null)
    {
        $tmp = $this->format('Y m d H i s');
        list($Y, $m, $d, $H, $i, $s) = explode(' ', $tmp);

        switch ($pattern) {
            case 'Y':
            case 'm':
            case 'd':
                $arg1 = 'Y';
                $arg2 = 'm';
                $arg3 = 'd';
                $method = 'setDate';
                break;
            case 'H':
            case 'i':
            case 's':
                $arg1 = 'H';
                $arg2 = 'i';
                $arg3 = 's';
                $method = 'setTime';
        }

        $old = $$pattern;

        if (!is_null($value)) {
            $$pattern = $value;
            $this->$method($$arg1, $$arg2, $$arg3);
        }

        return (int) $old;
    }

    public function ymd($separator = '-')
    {
        $patternSep   = '%1$s';
        $patternYear  = '%2$04d';
        $patternMonth = '%3$02d';
        $patternDay   = '%4$02d';

        $pattern = $patternYear . $patternSep . $patternMonth . $patternSep . $patternDay;

        $params = array($separator, $this->year(), $this->month(), $this->day());

        return $this->printf($pattern, $params);
    }

    public function dmy($separator = '/')
    {
        $patternSep   = '%1$s';
        $patternYear  = '%2$04d';
        $patternMonth = '%3$02d';
        $patternDay   = '%4$02d';

        $pattern = $patternDay . $patternSep . $patternMonth . $patternSep . $patternYear;

        $params = array($separator, $this->year(), $this->month(), $this->day());

        return $this->printf($pattern, $params);
    }

    public function hms($separator = ':')
    {
        $patternSep    = '%1$s';
        $patternHour   = '%2$02d';
        $patternMinute = '%3$02d';
        $patternSecond = '%4$02d';

        $pattern = $patternHour . $patternSep . $patternMinute . $patternSep . $patternSecond;

        $params = array($separator, $this->hour(), $this->minute(), $this->second());

        return $this->printf($pattern, $params);
    }

    protected function printf($pattern, $params)
    {
        return vsprintf($pattern, $params);
    }

    public function truncateTo($what)
    {
        $year   = $this->year();
        $month  = $this->month();
        $day    = $this->day();
        $hour   = $this->hour();
        $minute = $this->minute();
        $second = $this->second();

        switch (strtolower($what)) {
            case 'year':
            case 'years':
                $month  = 0;
                // no break

            case 'month':
            case 'months':
                $day    = 0;
                // no break

            case 'day':
            case 'days':
                $hour   = 0;
                // no break

            case 'hour':
            case 'hours':
                $minute = 0;
                // no break

            case 'minute':
            case 'minutes':
                $second = 0;
                // no break
        }

        parent::setDate($year, $month, $day);
        parent::setTime($hour, $minute, $second);

        return $this;
    }
}
