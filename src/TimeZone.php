<?php

/**
 * Better dates and times
 *
 * @author Tiross
 */
namespace Tiross\DateTime;

/**
 * Representation of time zone
 *
 * @author Tiross
 * @method self clone() Return a clone of actual instance
 * @property self $clone Use as a shortcut for method clone()
 * @property array $getLocation Use as a shortcut for method getLocation()
 * @property string $getName Use as a shortcut for method getName()
 * @property array $getTransitions Use as a shortcut for method getTransitions()
 * @property array $listAbbreviations Use as a shortcut for method listAbbreviations()
 * @property array $listIdentifiers Use as a shortcut for method listIdentifiers()
 */
class TimeZone extends \DateTimeZone
{
    /**
     * Creates new TimeZone object
     *
     * @see http://php.net/manual/en/timezones.php List of supported timezone names
     * @param string $timezone One of the supported timezone names
     * @throws Exception\InvalidTimeZoneException Thrown if supplied timezone is not recognised as
     *   a valid timezone
     */
    public function __construct($timezone)
    {
        $tz = $timezone;

        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            if ($tmp = $this->nameFromOffset($tz)) {
                $tz = $tmp;
            }
        }

        try {
            parent::__construct($tz);
        } catch (\Exception $e) {
            $message = sprintf('The timezone "%s" is not recognised as a valid timezone', $timezone);
            throw new Exception\InvalidTimeZoneException($message, 201, $e);
        }
    }

    /**
     * Convert any \DateTimeZone object to a \DateTime\TimeZone object
     *
     * @param \DateTimeZone $zone Object to convert
     * @return TimeZone Converted object
     */
    public static function convert(\DateTimeZone $zone)
    {
        if ($zone instanceof static) {
            return $zone;
        }

        return new static($zone->getName());
    }

    /**
     * Transtype a timezone to string
     *
     * @see http://php.net/manual/en/timezones.php list of timezone names
     * @return string One of the timezone names in the list of timezones.
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Return a clone of this object
     * @return self
     * @ignore
     */
    public function cloneObject()
    {
        return clone $this;
    }

    /**
     * @ignore
     */
    public function __call($method, $arguments)
    {
        switch (strtolower($method)) {
            case 'clone':
                return $this->cloneObject();
        }

        $message = sprintf('Call to undefined method %s::%s()', __CLASS__, $method);
        throw new Exception\LogicException($message, 299);
    }

    /**
     * @ignore
     */
    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'clone':
            case 'getlocation':
            case 'getname':
            case 'gettransitions':
            case 'listabbreviations':
            case 'listidentifiers':
                return $this->$property();
        }

        $message = sprintf('Undefined property: %s::$%s', __CLASS__, $property);
        throw new Exception\LogicException($message, 298);
    }

    public function getOffset($datetime)
    {
        if ($datetime instanceof \DateTime) {
            $obj  = new Duration(array('seconds' => parent::getOffset($datetime)), $datetime);

            return $obj;//->linearize();
        }

        $message = 'First argument is not a valid date';
        throw new Exception\InvalidDateTimeException($message, 203);
    }

    /**
     * Gets or sets the default timezone used by all date/time functions in a script
     *
     * If `$timezone` is not provided, no change will be made, the function will act like a getter.
     *
     * If `$timezone` is provided, it will be used to change the default timezone.
     * The method will return the old default value.
     *
     * @param  string|null $timezone The new default timezone
     * @return string
     * @throws Exception\InvalidTimeZoneException Thrown if supplied timezone is not recognised as
     *   a valid timezone
     */
    public static function defaultZone($timezone = null)
    {
        $default = date_default_timezone_get();

        if (!is_null($timezone)) {
            $result = date_default_timezone_set($timezone);

            if (false === $result) {
                $message = sprintf('The timezone "%s" is not recognised as a valid timezone', $timezone);
                throw new Exception\InvalidTimeZoneException($message, 202);
            }
        }

        return $default;
    }

    /**
     * Gets the version of the timezonedb
     *
     * If you get `0.system` you have the version that PHP shipped with.
     * For a newer version, you must upgrade via the PECL extension `sudo pecl install timezonedb`.
     *
     * @see http://php.net/manual/en/function.timezone-version-get.php Documentation on PHP.net
     * @see http://pecl.php.net/package/timezonedb timezonedb on PECL
     * @return string
     */
    public static function version()
    {
        return timezone_version_get();
    }

    protected function nameFromOffset($offset)
    {
        $names = array(
                0 => 'UTC',
              100 => 'Africa/Lagos',
              200 => 'Africa/Cairo',
              300 => 'Antarctica/Syowa',
              400 => 'Asia/Dubai',
              430 => 'Asia/Kabul',
              500 => 'Antarctica/Mawson',
              530 => 'Asia/Colombo',
              545 => 'Asia/Kathmandu',
              600 => 'Antarctica/Vostok',
              630 => 'Asia/Rangoon',
              700 => 'Indian/Christmas',
              800 => 'Asia/Singapore',
              845 => 'Australia/Eucla',
              900 => 'Asia/Seoul',
              930 => 'Australia/Darwin',
             1000 => 'Pacific/Chuuk',
             1100 => 'Pacific/Noumea',
             1130 => 'Pacific/Norfolk',
             1200 => 'Pacific/Wake',
             1300 => 'Pacific/Tongatapu',
             1400 => 'Pacific/Kiritimati',
             -100 => 'Atlantic/Cape_Verde',
             -200 => 'America/Noronha',
             -300 => 'America/Cayenne',
             -400 => 'America/Curacao',
             -430 => 'America/Caracas',
             -500 => 'America/Panama',
             -600 => 'Pacific/Galapagos',
             -700 => 'America/Phoenix',
             -800 => 'Pacific/Pitcairn',
             -900 => 'Pacific/Gambier',
             -930 => 'Pacific/Marquesas',
            -1000 => 'Pacific/Honolulu',
            -1100 => 'Pacific/Midway',
        );

        $tmp = str_replace(':', '', $offset);

        if (in_array(strtolower($tmp), array('z', 'zulu'))) {
            $tmp = 0;
        }

        if (!is_numeric($tmp)) {
            return null;
        }

        $tmp = (int) $tmp;

        if (array_key_exists($tmp, $names)) {
            return $names[ $tmp ];
        }

        return false;
    }
}
