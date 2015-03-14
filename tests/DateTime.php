<?php

namespace Tiross\DateTime\test\unit;

use Tiross\DateTime\DateTime as testedClass;
use Tiross\DateTime\TimeZone;

class DateTime extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\DateTime')
                ->isSubclassOf('\DateTime')
        ;
    }

    public function test__construct()
    {
        $this
            ->given($year = rand(2000, 2020))
            ->and($month = rand(1, 12))
            ->and($day = rand(1, 28)) // avoid rand to make a date like 31/02
            ->and($hour = rand(0, 24))
            ->and($minute = rand(0, 59))
            ->and($second = rand(0, 59))
            ->and($zoneParis = 'Europe/Paris')
            ->and($zoneSydney = 'Australia/Sydney')
            ->and($zoneNY = 'America/New_York')
            ->and($zoneTokyo = 'Asia/Tokyo')
            ->and($zoneOffset = sprintf('%+03d:00', rand(-10, 10)))

            ->and($date = compact('year', 'month', 'day'))
            ->and($time = compact('hour', 'minute', 'second'))
            ->and($datetime = array_merge($date, $time))

            ->if($string = vsprintf('%d-%02d-%02dT%02d:%02d:%02d', $datetime))
            ->then
                ->assert('New instance from string without timezone')
                    ->dateTime($this->newTestedInstance($string))
                        ->hasDateAndTime($year, $month, $day, $hour, $minute, $second)
                        ->hasTimezone(new TimeZone(TimeZone::defaultZone()))

                ->assert('New instance from string with timezone')
                    ->dateTime($this->newTestedInstance($string, $zoneParis))
                        ->hasDateAndTime($year, $month, $day, $hour, $minute, $second)
                        ->hasTimezone(new TimeZone($zoneParis))

                ->assert('New instance from array with timezone as parameter')
                    ->dateTime($this->newTestedInstance($datetime, new TimeZone($zoneSydney)))
                        ->hasDateAndTime($year, $month, $day, $hour, $minute, $second)
                        ->hasTimezone(new TimeZone($zoneSydney))

                ->assert('New instance from array with timezone in it')
                    ->if($params = array_merge($datetime, array('timezone' => $zoneTokyo)))
                    ->then
                        ->dateTime($this->newTestedInstance($params, new TimeZone($zoneSydney)))
                            ->hasDateAndTime($year, $month, $day, $hour, $minute, $second)
                            ->hasTimezone(new TimeZone($zoneTokyo))

                ->assert('New instance from array with only date with timezone')
                    ->dateTime($this->newTestedInstance($date, new \DateTimeZone($zoneNY)))
                        ->hasDate($year, $month, $day)
                        ->hasTime(0, 0, 0)
                        ->hasTimezone(new TimeZone($zoneNY))
        ;
    }

    /** @php < 5.5 */
    public function test__construct_Before55()
    {
        $this
            ->given($year = rand(2000, 2020))
            ->and($month = rand(1, 12))
            ->and($day = rand(1, 28)) // avoid rand to make a date like 31/02
            ->and($hour = rand(0, 24))
            ->and($minute = rand(0, 59))
            ->and($second = rand(0, 59))
            ->and($zoneParis = 'Europe/Paris')
            ->and($zoneSydney = 'Australia/Sydney')
            ->and($zoneNY = 'America/New_York')
            ->and($zoneTokyo = 'Asia/Tokyo')
            ->and($zoneOffset = sprintf('%+03d:00', rand(-10, 10)))

            ->and($date = compact('year', 'month', 'day'))
            ->and($time = compact('hour', 'minute', 'second'))
            ->and($datetime = array_merge($date, $time))

            ->if($string = vsprintf('%d-%02d-%02dT%02d:%02d:%02d', $datetime))
            ->then
                ->assert('New instance from string, timezone is set in string')
                    ->dateTime($this->newTestedInstance($string . $zoneOffset, $zoneNY))
                        ->hasDateAndTime($year, $month, $day, $hour, $minute, $second)
                        ->hasTimezone(new TimeZone($zoneOffset))
        ;
    }

    public function testClone()
    {
        $this
            ->given($year = rand(2000, 2020))
            ->and($month = rand(1, 12))
            ->and($day = rand(1, 28)) // avoid rand to make a date like 31/02
            ->and($hour = rand(0, 24))
            ->and($minute = rand(0, 59))
            ->and($second = rand(0, 59))

            ->if($datetime = compact('year', 'month', 'day', 'hour', 'minute', 'second'))

            ->then
                ->object($this->newTestedInstance($datetime))
                    ->isCloneOf($this->testedInstance->cloneObject()) // not recommanded
                    ->isCloneOf($this->testedInstance->clone())
                    ->isCloneOf($this->testedInstance->CLONE())
                    ->isCloneOf($this->testedInstance->clone)
                    ->isCloneOf($this->testedInstance->CLONE)
        ;
    }

    public function test__call()
    {
        $this
            ->if($obj = $this->newTestedInstance)
            ->and($method = uniqid())
            ->then
                ->exception(function () use ($obj, $method) {
                    $obj->$method();
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\LogicException')
                    ->hasCode(199)
                    ->hasMessage(sprintf('Call to undefined method %s::%s()', get_class($obj), $method))
        ;
    }

    public function test__get()
    {
        $this
            ->if($obj = $this->newTestedInstance)
            ->and($property = uniqid())
            ->then
                ->exception(function () use ($obj, $property) {
                    $obj->$property;
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\LogicException')
                    ->hasCode(198)
                    ->hasMessage(sprintf('Undefined property: %s::$%s', get_class($obj), $property))
        ;
    }

    public function testNow()
    {
        $this
            ->if($dt = new \DateTime)
            ->then
                ->dateTime(testedClass::now())
                    ->isEqualTo($dt)
        ;
    }
}
