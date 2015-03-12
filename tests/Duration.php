<?php

namespace Tiross\DateTime\tests\unit;

use Tiross\DateTime\Duration as testedClass;
use Tiross\DateTime\DateTime;
use Tiross\DateTime\TimeZone;

class Duration extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\Duration')
                ->hasNoParent
        ;
    }

    public function test__construct()
    {
        $this
            ->object($this->newTestedInstance('PT1H24M'))
                ->isEqualTo($this->newTestedInstance(array('hours' => 1, 'minutes' => 24)))
                ->isEqualTo($this->newTestedInstance('01:24:00'))
                ->isEqualTo($this->newTestedInstance('01:24'))

            ->object($this->newTestedInstance('P0M'))
                ->isEqualTo($this->newTestedInstance())
                ->isEqualTo($this->newTestedInstance(null))
                ->isEqualTo($this->newTestedInstance(array('invalid' => uniqid())))
                ->isEqualTo($this->newTestedInstance('P0Y0M0W0DT0H0M0S'))

            ->object($this->newTestedInstance('-P12MT1M'))
                ->isEqualTo($this->newTestedInstance('-P1YT1M'))
                ->isEqualTo($this->newTestedInstance(array('years' => -1, 'minutes' => -1)))

            ->object($this->newTestedInstance('-P12MT1M', $ref = new DateTime)->getReferenceDate())
                ->isIdenticalTo($ref)

            ->if($errorArgs = new \stdClass)
            ->then
                ->exception(function () use ($errorArgs) {
                    new testedClass($errorArgs);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidDurationException')
                    ->hasCode(301)
                    ->hasMessage(sprintf('Argument seems invalid "%s"', str_replace("\n", '', print_r($errorArgs, true))))

            ->if($errorArgs = uniqid())
            ->then
                ->exception(function () use ($errorArgs) {
                    new testedClass($errorArgs);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidDurationException')
                    ->hasCode(301)
                    ->hasMessage(sprintf('Argument seems invalid "%s(%s)"', gettype($errorArgs), $errorArgs))

            ->if($errorArgs = new TimeZone('UTC'))
            ->then
                ->exception(function () use ($errorArgs) {
                    new testedClass($errorArgs);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\InvalidDurationException')
                    ->hasCode(301)
                    ->hasMessage(sprintf('Argument seems invalid "%s(%s)"', get_class($errorArgs), (string) $errorArgs))
        ;
    }

    public function testFromDateInterval()
    {
        $this
            ->given($calendar = array('Y', 'M', 'W', 'D'))
            ->and($clock = array('H', 'M', 'S'))
            ->when(function () use (&$calendar, &$clock) {
                shuffle($calendar);
                shuffle($clock);
            })

            ->if($string = 'P' . rand(0, 30) . current($calendar) . 'T' . rand(0, 30) . current($clock))

            ->then
                ->object(testedClass::fromDateInterval(new \DateInterval($string)))
                    ->isInstanceOf('\Tiross\DateTime\Duration')
                    ->isEqualTo($this->newTestedInstance($string))
        ;
    }

    public function testClone()
    {
        $this
            ->given($calendar = array('Y', 'M', 'W', 'D'))
            ->and($clock = array('H', 'M', 'S'))
            ->when(function () use (&$calendar, &$clock) {
                shuffle($calendar);
                shuffle($clock);
            })

            ->if($string = 'P' . rand(0, 30) . current($calendar) . 'T' . rand(0, 30) . current($clock))

            ->then
                ->object($this->newTestedInstance($string))
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
                    ->hasCode(399)
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
                    ->hasCode(398)
                    ->hasMessage(sprintf('Undefined property: %s::$%s', get_class($obj), $property))
        ;
    }
}
