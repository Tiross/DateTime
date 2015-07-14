<?php

namespace Tiross\DateTime\tests\unit;

use Tiross\DateTime\TimeZone as testedClass;

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
            ->object($this->newTestedInstance($tz))
                ->isInstanceOf('\Tiross\DateTime\TimeZone')
                ->isNotCallable

            ->if($errorTZ = $tz . '/Error')
            ->then
                ->exception(function () use ($errorTZ) {
                    new testedClass($errorTZ);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidTimeZoneException')
                    ->hasCode(201)
                    ->hasMessage(sprintf('The timezone "%s" is not recognised as a valid timezone', $errorTZ))
        ;
    }

    /**
     * @dataProvider constructProvider
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
     * @dataProvider constructProvider
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
            '+03:00',
            '-01:00',
            sprintf('%+03d:00', rand(-10, 10)),
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
}
