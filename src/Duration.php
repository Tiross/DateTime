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

    /** @var float Months */
    protected $months = 0;

    /** @var float Days */
    protected $days = 0;

    /** @var float Minutes */
    protected $minutes = 0;

    /** @var float Seconds */
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
        $this->months  = $multiplier * ($years * 12 + $months);
        $this->days    = $multiplier * ($weeks * 7 + $days);
        $this->minutes = $multiplier * ($hours * 60 + $minutes);
        $this->seconds = $multiplier * $seconds;

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
     * Chain cloning
     *
     * @return self
     * @internal
     */
    public function cloneObject()
    {
        return clone $this;
    }

    /**
     * @internal
     */
    public function __call($method, $arguments)
    {
        switch (strtolower($method)) {
            case 'clone':
                return $this->cloneObject();

            case 'years':
            case 'months':
            case 'weeks':
            case 'days':
            case 'hours':
            case 'minutes':
            case 'seconds':
                $tmp = $this->inUnits($method);
                return array_shift($tmp);
        }

        $message = sprintf('Call to undefined method %s::%s()', __CLASS__, $method);
        throw new Exception\LogicException($message, 399);
    }

    /**
     * @internal
     */
    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'clone':

            case 'haspositive':
            case 'hasnegative':
            case 'iszero':
            case 'ispositive':
            case 'isnegative':

            case 'years':
            case 'months':
            case 'weeks':
            case 'days':
            case 'hours':
            case 'minutes':
            case 'seconds':

            case 'inverse':
            case 'absolute':
                return $this->$property();
        }

        $message = sprintf('Undefined property: %s::$%s', __CLASS__, $property);
        throw new Exception\LogicException($message, 398);
    }


    /**
     * Check if the duration has positive non zero value
     *
     * @return bool
     */
    public function hasPositive()
    {
        $func = function ($val) {
            if ($val > 0) {
                return $val;
            }
        };

        $tmp = array(
            $this->months,
            $this->days,
            $this->minutes,
            $this->seconds,
        );

        return !!count(array_filter($tmp, $func));
    }

    /**
     * Check if the duration has negative non zero value
     *
     * @return bool
     */
    public function hasNegative()
    {
        $func = function ($val) {
            if ($val < 0) {
                return $val;
            }
        };

        $tmp = array(
            $this->months,
            $this->days,
            $this->minutes,
            $this->seconds,
        );

        return !!count(array_filter($tmp, $func));
    }


    /**
     * Check if duration is null
     *
     * @return bool
     */
    public function isZero()
    {
        $tmp = array(
            $this->months,
            $this->days,
            $this->minutes,
            $this->seconds,
        );

        return !array_sum(array_map('abs', $tmp));
    }

    /**
     * Check if duration has _only_ positives values
     *
     * @return bool
     */
    public function isPositive()
    {
        return $this->hasPositive() && !$this->hasNegative();
    }

    /**
     * Check if duration has _only_ negatives values
     *
     * @return bool
     */
    public function isNegative()
    {
        return !$this->hasPositive() && $this->hasNegative();
    }

    /**
     * Returns the length of the duration in the units (any of those that can be passed to new) given as arguments
     *
     * All lengths are integral, but may be negative.
     * Smaller units are computed from what remains after taking away the larger units given, so for example:
     * ```php
     * my $dur = new Duration(['years' => 1, 'months' => 15]);
     *
     * $dur->in_units('years');             // ['years' => 2]
     * $dur->in_units('months');            // ['months' => 27]
     * $dur->in_units('years', 'months');   // ['years' => 2, 'months' => 3]
     * $dur->in_units(['years', 'months']); // ['years' => 2, 'months' => 3]
     * $dur->in_units('weeks', 'days');     // ['weeks' => 0, 'days' => 0] !!
     * ```
     *
     * The last example demonstrates that there will not be any conversion between units which don't have a fixed
     * conversion rate.
     * The only conversions possible are:
     *  - years <=> months
     *  - weeks <=> days
     *  - hours <=> minutes
     *
     * Note that the numbers returned by this method may not match the values given to the constructor.
     *
     * @param string|string[] $params
     * @return array
     */
    public function inUnits($params)
    {
        if (is_array($params)) {
            $args = $params;
        } else {
            $args = func_get_args();
        }
        $args = array_unique($args);
        $args = array_map('strtolower', $args);

        $units = array('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds');

        $asked   = array_intersect($units, $args);
        $results = array();

        $monthsMulti  = $this->months < 0 ? -1 : 1;
        $months       = abs($this->months);

        $daysMulti    = $this->days < 0 ? -1 : 1;
        $days         = abs($this->days);

        $minutesMulti = $this->minutes < 0 ? -1 : 1;
        $minutes      = abs($this->minutes);

        $secondsMulti = $this->seconds < 0 ? -1 : 1;
        $seconds      = abs($this->seconds);

        foreach ($asked as $unit) {

            switch ($unit) {
                case 'years':
                    $base  = 'months';
                    $coeff = 12;
                    break;

                case 'months':
                    $base  = 'months';
                    $coeff = 1;
                    break;

                case 'weeks':
                    $base  = 'days';
                    $coeff = 7;
                    break;

                case 'days':
                    $base  = 'days';
                    $coeff = 1;
                    break;

                case 'hours':
                    $base  = 'minutes';
                    $coeff = 60;
                    break;

                case 'minutes':
                    $base  = 'minutes';
                    $coeff = 1;
                    break;

                case 'seconds':
                default: // fix scrutinizer issue
                    $base  = 'seconds';
                    $coeff = 1;
                    break;
            }

            // Math
            $value = floor($$base / $coeff);
            $$base %= $coeff;

            $value *= ${$base . 'Multi'};

            $results[ $unit ] = (int) $value;
        }

        uksort($results, function ($key1, $key2) use ($args) {
            return array_search($key1, $args) > array_search($key2, $args);
        });

        return $results;
    }

    /**
     * Multiply all notion of duration
     *
     * @param  float $factor
     * @return self
     */
    public function multiply($factor)
    {
        $factor = (float) $factor;

        $this->months  = intval($this->months * $factor);
        $this->days    = intval($this->days * $factor);
        $this->minutes = intval($this->minutes * $factor);
        $this->seconds = intval($this->seconds * $factor);

        return $this;
    }

    /**
     * Make negative a positive duration or make positive a negative duration
     *
     * Basically, it's a shortcut for `$this->multiply(-1)`
     *
     * @return self
     */
    public function inverse()
    {
        return $this->multiply(-1);
    }

    /**
     * Change all values to absolute ones
     * @return self
     */
    public function absolute()
    {
        $this->months  = abs($this->months());
        $this->days    = abs($this->days());
        $this->minutes = abs($this->minutes());
        $this->seconds = abs($this->seconds());

        return $this;
    }
}
