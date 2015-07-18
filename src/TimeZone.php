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
            2400 => 'UTC',
            2500 => 'Africa/Lagos',
            2600 => 'Africa/Cairo',
            2700 => 'Antarctica/Syowa',
            2800 => 'Asia/Dubai',
            2830 => 'Asia/Kabul',
            2900 => 'Antarctica/Mawson',
            2930 => 'Asia/Colombo',
            2945 => 'Asia/Kathmandu',
            3000 => 'Antarctica/Vostok',
            3030 => 'Asia/Rangoon',
            3100 => 'Indian/Christmas',
            3200 => 'Asia/Singapore',
            3245 => 'Australia/Eucla',
            3300 => 'Asia/Seoul',
            3330 => 'Australia/Darwin',
            3400 => 'Pacific/Chuuk',
            3500 => 'Pacific/Noumea',
            3530 => 'Pacific/Norfolk',
            3600 => 'Pacific/Wake',
            3700 => 'Pacific/Tongatapu',
            3800 => 'Pacific/Kiritimati',
            2300 => 'Atlantic/Cape_Verde',
            2200 => 'America/Noronha',
            2100 => 'America/Cayenne',
            2000 => 'America/Curacao',
            2030 => 'America/Caracas',
            1900 => 'America/Panama',
            1800 => 'Pacific/Galapagos',
            1700 => 'America/Phoenix',
            1600 => 'Pacific/Pitcairn',
            1500 => 'Pacific/Gambier',
            1530 => 'Pacific/Marquesas',
            1400 => 'Pacific/Honolulu',
            1300 => 'Pacific/Midway',
        );

        $o = str_replace(':', '', $offset);

        if (!is_numeric($o)) {
            return null;
        }

        $o += 2400;

        if (array_key_exists($o, $names)) {
            return $names[ $o ];
        }

        return false;
    }
}
