<?php

namespace Tiross\DateTime\tests\unit;

use Tiross\DateTime\TimeZone as testedClass;
use Tiross\DateTime\DateTime;

class TimeZone extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\TimeZone')
                ->isSubclassOf('\DateTimeZone')
        ;
    }

    /**
     * @dataProvider constructProvider
     */
    public function test__construct($tz)
    {
        $this
            ->if($errorTZ = 'Error/' . $tz)

            ->assert($tz)
                ->object($this->newTestedInstance($tz))
                    ->isInstanceOf('\Tiross\DateTime\TimeZone')
                    ->isNotCallable

            ->assert('Bad timezone raises exceptions / ' . $errorTZ)
                ->exception(function () use ($errorTZ) {
                    new testedClass($errorTZ);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidTimeZoneException')
                    ->hasCode(201)
                    ->hasMessage(sprintf('The timezone "%s" is not recognised as a valid timezone', $errorTZ))
        ;
    }

    /**
     * @dataProvider timezoneProvider
     */
    public function testConvert($tz)
    {
        $this
            ->if($this->newTestedInstance($tz))
            ->then
                ->object(testedClass::convert(new \DateTimeZone($tz)))
                    ->isEqualTo($this->testedInstance)

                ->object(testedClass::convert($this->testedInstance))
                    ->isTestedInstance
        ;
    }

    /**
     * @dataProvider timezoneProvider
     */
    public function testCastToString($tz)
    {
        $this
            ->castToString($this->newTestedInstance($tz))
                ->isIdenticalTo($tz)
        ;
    }

    /**
     * @dataProvider constructProvider
     */
    public function testClone($tz)
    {
        $this
            ->object($this->newTestedInstance($tz))
                ->isCloneOf($this->testedInstance->clone())
                ->isCloneOf($this->testedInstance->CLONE())
                ->isCloneOf($this->testedInstance->clone)
                ->isCloneOf($this->testedInstance->CLONE)
                ->isCloneOf($this->testedInstance->cloneObject())
        ;
    }

    public function test__call()
    {
        $this
            ->if($obj = $this->newTestedInstance('UTC'))
            ->and($method = uniqid())
            ->then
                ->exception(function () use ($obj, $method) {
                    $obj->$method();
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\LogicException')
                    ->hasCode(299)
                    ->hasMessage(sprintf('Call to undefined method %s::%s()', get_class($obj), $method))
        ;
    }

    /**
     * @dataProvider getProvider
     */
    public function test__get($method)
    {
        $this
            ->if($obj = $this->newTestedInstance('UTC'))
            ->then
                ->variable($this->testedInstance->$method)
                    ->isIdenticalTo($this->testedInstance->$method())

            ->if($property = $method . uniqid())
            ->then
                ->exception(function () use ($obj, $property) {
                    $obj->$property;
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\LogicException')
                    ->hasCode(298)
                    ->hasMessage(sprintf('Undefined property: %s::$%s', get_class($obj), $property))
        ;
    }

    public function testVersion()
    {
        $this
            ->if($this->function->timezone_version_get = $version = uniqid())
            ->then
                ->string(testedClass::version())
                    ->isIdenticalTo($version)
                ->function('timezone_version_get')
                    ->wasCalledWithoutAnyArgument()
                        ->once
        ;
    }

    public function testDefault()
    {
        $this
            ->given($timezone = uniqid())

            ->if($this->function->date_default_timezone_get = $default = uniqid())
            ->and($this->function->date_default_timezone_set = true)

            ->assert('Get actual default')
                ->string(testedClass::defaultZone())
                    ->isIdenticalTo($default)
                ->function('date_default_timezone_get')
                    ->wasCalledWithoutAnyArgument()->once
                ->function('date_default_timezone_set')
                    ->wasCalled()->never

            ->assert('Set new default')
                ->string(testedClass::defaultZone($timezone))
                    ->isIdenticalTo($default)
                ->function('date_default_timezone_get')
                    ->wasCalledWithoutAnyArgument()->once
                ->function('date_default_timezone_set')
                    ->wasCalledWithIdenticalArguments($timezone)->once

            ->if($this->function->date_default_timezone_set = false)
            ->assert('Bad timezone')
                ->exception(function () use ($timezone) {
                    testedClass::defaultZone($timezone);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidTimeZoneException')
                    ->hasCode(202)
                    ->hasMessage(sprintf('The timezone "%s" is not recognised as a valid timezone', $timezone))
        ;
    }

    public function constructProvider()
    {
        return array_merge($this->timezoneProvider(), $this->offsetProvider());
    }

    public function timezoneProvider()
    {
        return array(
            'UTC',
            'America/Dominica',
            'America/Montreal',
            'Asia/Calcutta',
            'Asia/Singapore',
            'Australia/Adelaide',
            'Australia/NSW',
            'Australia/Melbourne',
            'Australia/Queensland',
            'Australia/Victoria',
            'Europe/Lisbon',
            'Europe/Paris',
            'Europe/Prague',
            'Europe/Rome',
        );
    }

    public function offsetProvider()
    {
        return array(
            sprintf('%+03d:00', rand(-10, 10)),
            'Z',
            'Zulu',
            '-11:00',
            '-10:00',
            '-09:30',
            '-09:00',
            '-08:00',
            '-07:00',
            '-06:00',
            '-05:00',
            '-04:30',
            '-04:00',
            '-03:00',
            '-02:00',
            '-01:00',
            '-00:00',
            '+00:00',
            '+01:00',
            '+02:00',
            '+03:00',
            '+04:00',
            '+04:30',
            '+05:00',
            '+05:30',
            '+05:45',
            '+06:00',
            '+06:30',
            '+07:00',
            '+08:00',
            '+08:45',
            '+09:00',
            '+09:30',
            '+10:00',
            '+11:00',
            '+11:30',
            '+12:00',
            '+13:00',
            '+14:00',
        );
    }

    public function getProvider()
    {
        return array(
            'getLocation',
            'getName',
            'getTransitions',
            'listAbbreviations',
            'listIdentifiers',
            'GeTlOcAtIoN',
        );
    }

    /**
     * @dataProvider timezoneProvider
     */
    public function testGetOffset($timezone)
    {
        $this
            ->if($obj = $this->newTestedInstance($timezone))
            ->and($date = new DateTime)
            ->then
                ->object($offset = $this->testedInstance->getOffset($date))
                    ->isInstanceOf('\Tiross\DateTime\Duration')

                ->object($offset->getReferenceDate())
                    ->isIdenticalTo($date)

                ->integer($offset->seconds())
                    ->isIdenticalTo(timezone_offset_get(new \DateTimeZone($timezone), new \DateTime))

                ->exception(function () use ($obj) {
                    $obj->getOffset(uniqid());
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidDateTimeException')
                    ->hasCode(203)
                    ->hasMessage('First argument is not a valid date')
        ;
    }
}
