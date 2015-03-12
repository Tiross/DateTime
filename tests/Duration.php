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

    public function testHasPositive()
    {
        $this
            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->hasPositive())->isTrue
                ->boolean($this->testedInstance->HASPOSITIVE())->isTrue
                ->boolean($this->testedInstance->hasPositive)->isTrue
                ->boolean($this->testedInstance->HASPOSITIVE)->isTrue

            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => 1)))
            ->then
                ->boolean($this->testedInstance->hasPositive())->isTrue
                ->boolean($this->testedInstance->HASPOSITIVE())->isTrue
                ->boolean($this->testedInstance->hasPositive)->isTrue
                ->boolean($this->testedInstance->HASPOSITIVE)->isTrue

            ->if($this->newTestedInstance(array('years' => -1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->hasPositive())->isFalse
                ->boolean($this->testedInstance->HASPOSITIVE())->isFalse
                ->boolean($this->testedInstance->hasPositive)->isFalse
                ->boolean($this->testedInstance->HASPOSITIVE)->isFalse

            ->if($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->hasPositive())->isFalse
                ->boolean($this->testedInstance->HASPOSITIVE())->isFalse
                ->boolean($this->testedInstance->hasPositive)->isFalse
                ->boolean($this->testedInstance->HASPOSITIVE)->isFalse
        ;
    }

    public function testHasNegative()
    {
        $this
            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->hasNegative())->isTrue
                ->boolean($this->testedInstance->HASNEGATIVE())->isTrue
                ->boolean($this->testedInstance->hasNegative)->isTrue
                ->boolean($this->testedInstance->HASNEGATIVE)->isTrue

            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => 1)))
            ->then
                ->boolean($this->testedInstance->hasNegative())->isFalse
                ->boolean($this->testedInstance->HASNEGATIVE())->isFalse
                ->boolean($this->testedInstance->hasNegative)->isFalse
                ->boolean($this->testedInstance->HASNEGATIVE)->isFalse

            ->if($this->newTestedInstance(array('years' => -1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->hasNegative())->isTrue
                ->boolean($this->testedInstance->HASNEGATIVE())->isTrue
                ->boolean($this->testedInstance->hasNegative)->isTrue
                ->boolean($this->testedInstance->HASNEGATIVE)->isTrue

            ->if($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->hasNegative())->isFalse
                ->boolean($this->testedInstance->HASNEGATIVE())->isFalse
                ->boolean($this->testedInstance->hasNegative)->isFalse
                ->boolean($this->testedInstance->HASNEGATIVE)->isFalse
        ;
    }

    public function testIsZero()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->isZero())->isTrue
                ->boolean($this->testedInstance->ISZERO())->isTrue
                ->boolean($this->testedInstance->isZero)->isTrue
                ->boolean($this->testedInstance->ISZERO)->isTrue

            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => 1)))
            ->then
                ->boolean($this->testedInstance->isZero())->isFalse
                ->boolean($this->testedInstance->ISZERO())->isFalse
                ->boolean($this->testedInstance->isZero)->isFalse
                ->boolean($this->testedInstance->ISZERO)->isFalse
        ;
    }

    public function testIsPositive()
    {
        $this
            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->isPositive())->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE())->isFalse
                ->boolean($this->testedInstance->isPositive)->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE)->isFalse

            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => 1)))
            ->then
                ->boolean($this->testedInstance->isPositive())->isTrue
                ->boolean($this->testedInstance->ISPOSITIVE())->isTrue
                ->boolean($this->testedInstance->isPositive)->isTrue
                ->boolean($this->testedInstance->ISPOSITIVE)->isTrue

            ->if($this->newTestedInstance(array('years' => -1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->isPositive())->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE())->isFalse
                ->boolean($this->testedInstance->isPositive)->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE)->isFalse

            ->if($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->isPositive())->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE())->isFalse
                ->boolean($this->testedInstance->isPositive)->isFalse
                ->boolean($this->testedInstance->ISPOSITIVE)->isFalse
        ;
    }

    public function testIsNegative()
    {
        $this
            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->isNegative())->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE())->isFalse
                ->boolean($this->testedInstance->isNegative)->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE)->isFalse

            ->if($this->newTestedInstance(array('years' => 1, 'minutes' => 1)))
            ->then
                ->boolean($this->testedInstance->isNegative())->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE())->isFalse
                ->boolean($this->testedInstance->isNegative)->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE)->isFalse

            ->if($this->newTestedInstance(array('years' => -1, 'minutes' => -1)))
            ->then
                ->boolean($this->testedInstance->isNegative())->isTrue
                ->boolean($this->testedInstance->ISNEGATIVE())->isTrue
                ->boolean($this->testedInstance->isNegative)->isTrue
                ->boolean($this->testedInstance->ISNEGATIVE)->isTrue

            ->if($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->isNegative())->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE())->isFalse
                ->boolean($this->testedInstance->isNegative)->isFalse
                ->boolean($this->testedInstance->ISNEGATIVE)->isFalse
        ;
    }

}
