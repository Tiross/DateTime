<?php

/**
 * Better dates and times
 *
 * @author Tiross
 */
namespace Tiross\DateTime;

/**
 * Duration
 *
 * @author Tiross
 */
class Duration
{
    /** @var bool Is a finite duration ? */
    protected $isFinite = true;

    /** @var integer Months */
    protected $months = 0;

    /** @var integer Days */
    protected $days = 0;

    /** @var integer Minutes */
    protected $minutes = 0;

    /** @var integer Seconds */
    protected $seconds = 0;

    /** @var DateTime Reference to a DateTime */
    protected $reference = null;

    /**
     * Creating duration
     *
     * You have three way to create a duration:
     *  * using ISO-8601 representation
     *    ```php
     *    new Duration('P1DT1H');
     *    ```
     *  * using an array, with `years`, `months`, `weeks`, `days`, `hours`, `minutes` and `seconds` keys
     *    ```php
     *    new Duration(['days' => 1, 'hours' => 1]);
     *    ```
     *  * using a string (only for hours, minutes, seconds based duration)
     *    ```php
     *    new Duration('01:30:00');
     *    ```
     *
     * You can pass a reference date.
     * It's necessary for doing some math and convertion.
     *
     * @param string|integer[] $options
     * @param DateTime|null $reference
     */
    public function __construct($options = array(), DateTime $reference = null)
    {
        $negative = false;

        if (is_string($options)) {

            // Regex for ISO format, PxYxMxWxDTxHxMxS
            $regex = '`P(?:(\d+)Y)?(?:(\d+)M)?(?:(\d+)W)?(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?)?`';

            // Test if it's a negative duration
            $negative = $options[0] == '-';
            if ($negative) {
                $options = substr($options, 1);
            }

            // If the string form hh:mm:ss is used
            if ($options[0] != 'P' && strpos($options, ':') !== false) {
                $args = array('hours' => 0, 'minutes' => 0, 'seconds' => 0);

                // transforming hh:mm to hh:mm:ss
                if (substr_count($options, ':') == 1) {
                    $options .= ':0';
                }

                list($args['hours'], $args['minutes'], $args['seconds']) = explode(':', $options);

            // ISO format ?
            } elseif (preg_match($regex, $options, $values)) {
                $units = array('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds');

                unset($values[0]); // Removing the full expression

                // Do we have some duration part ?
                if (count($values)) {
                    $tmpNb = count($units) - count($values);

                    if ($tmpNb > 0) {
                        $tmpComp = array_fill(0, $tmpNb, 0);
                        $tmpVal  = array_merge($values, $tmpComp);
                    } else {
                        $tmpVal  = array_values($values);
                    }

                    $args = array_combine($units, $tmpVal);
                }
            }

        } elseif (is_array($options)) {
            $args = $options;

        } elseif (is_null($options)) {
            $args = array();
        }

        // Normally, we have something
        if (!isset($args)) {
            $type   = gettype($options);
            $params = str_replace("\n", '', print_r($options, true));

            if ('object' === $type) {
                if (method_exists($options, '__toString')) {
                    $message = sprintf('"%s(%s)"', get_class($options), (string) $options);
                } else {
                    $message = sprintf('"%s"', $params);
                }
            } else {
                $message = sprintf('"%s(%s)"', $type, $params);
            }

            throw new Exception\InvalidDurationException('Argument seems invalid ' . $message, 301);
        }

        // Create a multiplier from sign
        $multiplier = $negative ? -1 : 1;

        // We only deal with integers
        $args = array_map('intval', $args);

        // Creating defaults
        $defaults = array(
            'years'   => 0,
            'months'  => 0,
            'weeks'   => 0,
            'days'    => 0,
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        );
        $args = array_merge($defaults, $args);
        extract($args);

        // Affecting
        $this->months  = intval($multiplier * ($years * 12 + $months));
        $this->days    = intval($multiplier * ($weeks * 7 + $days));
        $this->minutes = intval($multiplier * ($hours * 60 + $minutes));
        $this->seconds = intval($multiplier * $seconds);

        // Adding reference
        if ($reference instanceof DateTime) {
            $this->setReferenceDate($reference);
        }
    }

    /**
     * Add a reference date
     *
     * @param DateTime $reference
     * @return self
     */
    public function setReferenceDate(DateTime $reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Return the reference DateTime
     *
     * @return DateTime
     */
    public function getReferenceDate()
    {
        return $this->reference;
    }


    /**
     * Create a Duration from a DateInterval
     *
     * @param \DateInterval $obj
     * @return self
     */
    public static function fromDateInterval(\DateInterval $obj)
    {
        $multiplier = $obj->format('%r') === '-' ? -1 : 1;

        $args = array(
            'years'   => $multiplier * $obj->format('%y'),
            'months'  => $multiplier * $obj->format('%m'),
            'days'    => $multiplier * $obj->format('%d'),
            'hours'   => $multiplier * $obj->format('%h'),
            'minutes' => $multiplier * $obj->format('%i'),
            'seconds' => $multiplier * $obj->format('%s'),
        );

        return new Duration($args);
    }

    /**
     * Clone
     *
     * @return self
     * @internal
     */
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
        throw new Exception\LogicException($message, 399);
    }

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'clone':
                return $this->$property();
        }

        $message = sprintf('Undefined property: %s::$%s', __CLASS__, $property);
        throw new Exception\LogicException($message, 398);
    }
}
