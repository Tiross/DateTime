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
                    ->isInstanceOf('\Tiross\DateTime\InvalidTimeZoneException')
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

    public function test__callException()
    {
        $this
            ->if($obj = $this->newTestedInstance('UTC'))
            ->and($method = uniqid())
            ->then
                ->exception(function () use ($method) {
                    $this->testedInstance->$method();
                })
                    ->hasCode(204)
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
                ->exception(function () use ($property) {
                    $this->testedInstance->$property;
                })
                    ->isInstanceOf('\Tiross\DateTime\LogicException')
                    ->hasCode(205)
                    ->hasMessage(sprintf('Undefined property: %s::$%s', get_class($obj), $property))
        ;
    }


    public function constructProvider()
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

    public function getProvider()
    {
        return array(
            'GeTlOcAtIoN',
            'getlocation',
            'getname',
            'gettransitions',
            'listabbreviations',
            'listidentifiers',
        );
    }
}
