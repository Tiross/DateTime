<?php
/**
 * Date and Time
 *
 * @package DateTime
 * @author Tiross
 */
namespace Tiross\DateTime;

/**
 * Representation of time zone
 *
 * @author Tiross
 */
class TimeZone extends \DateTimeZone
{
    /**
     * Creates new TimeZone object
     *
     * @see http://php.net/manual/en/timezones.php list of supported timezone names
     * @param string $timezone One of the supported timezone names
     * @throws \DateTime\InvalidTimeZoneException Thrown if supplied timezone is not recognised as a valid timezone.
     */
    public function __construct($timezone)
    {
        try {
            parent::__construct($timezone);
        } catch (\Exception $e) {
            $message = sprintf('The timezone "%s" is not recognised as a valid timezone', $timezone);
            throw new InvalidTimeZoneException($message, 201, $e);
        }
    }

    /**
     * Convert any \DateTimeZone object to a \DateTime\TimeZone object
     *
     * @param \DateTimeZone $tz Object to convert
     * @return \DateTime\TimeZone Converted object
     */
    public static function convert(\DateTimeZone $tz)
    {
        if ($tz instanceof static) {
            return $tz;
        }

        return new static($tz->getName());
    }

    /**
     * Transtype a timezone to string
     *
     * @see http://php.net/manual/en/timezones.php list of timezone names
     * @return string One of the timezone names in the list of timezones.
     */
    public function __toString()
    {
        return parent::getName();
    }

    /**
     * Return a clone of this object
     * @return self
     */
    public function cloneObject()
    {
        return clone $this;
    }

    /**
     */
    public function __call($method, $arguments)
    {
        switch (strtolower($method)) {
            case 'clone':
                return $this->cloneObject();
        }

        $message = sprintf('Call to undefined method %s::%s()', __CLASS__, $method);
        throw new LogicException($message, 204);
    }

    /**
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
        throw new LogicException($message, 205);
    }
}
