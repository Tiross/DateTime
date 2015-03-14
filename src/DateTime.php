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
                return $this->$property();
        }

        $message = sprintf('Undefined property: %s::$%s', __CLASS__, $property);
        throw new Exception\LogicException($message, 198);
    }
}
