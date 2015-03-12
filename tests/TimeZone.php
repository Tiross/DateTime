<?php

namespace DateTime\tests\unit;

class TimeZone extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\DateTime\TimeZone')
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
                ->isInstanceOf('\DateTime\TimeZone')
                ->isInstanceOf('\DateTimeZone')
                ->isNotCallable

            ->if($errorTZ = $tz . '/Error')
            ->then
                ->exception(function () use ($errorTZ) {
                    new \DateTime\TimeZone($errorTZ);
                })
                    ->isInstanceOf('\DateTime\InvalidTimeZoneException')
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
            ->object(\DateTime\TimeZone::convert(new \DateTimeZone($tz)))
                ->isEqualTo(\DateTime\TimeZone::convert(new \DateTime\TimeZone($tz)))
                ->isEqualTo(new \DateTime\TimeZone($tz))
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
            ->if($this->newTestedInstance('UTC'))
            ->and($method = 'invalidMethod')
            ->then
                ->exception(function () use ($method) {
                    $this->testedInstance->$method();
                })
                    ->hasCode(204)
                    ->hasMessage(sprintf('Call to undefined method DateTime\TimeZone::%s()', $method))
        ;
    }

    /**
     * @dataProvider methodProvider
     */
    public function test__get($method)
    {
        $this
            ->if($this->newTestedInstance('UTC'))
            ->then
                ->variable($this->testedInstance->$method)
                    ->isIdenticalTo($this->testedInstance->$method())
        ;
    }

    public function test__getException()
    {
        $this
            ->if($this->newTestedInstance('UTC'))
            ->and($property = 'invalidProperty')
            ->then
                ->exception(function () use ($property) {
                    $this->testedInstance->$property;
                })
                    ->hasCode(205)
                    ->hasMessage(sprintf('Undefined property: DateTime\TimeZone::$%s', $property))
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

    public function methodProvider()
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
