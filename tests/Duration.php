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

    public function testInUnits()
    {
        $this
            ->given($years = rand(0, 100))
            ->and($months = rand(0, 100))
            ->and($weeks = rand(0, 100))
            ->and($days = rand(0, 100))
            ->and($hours = rand(0, 100))
            ->and($minutes = rand(0, 100))
            ->and($seconds = rand(0, 100) * -1)

            ->and($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))

            ->if($this->newTestedInstance($args))

            ->and($years += floor($months / 12))
            ->and($months %= 12)
            ->and($weeks += floor($days / 7))
            ->and($days %= 7)
            ->and($hours += floor($minutes / 60))
            ->and($minutes %= 60)

            ->and($years = (int) $years)
            ->and($months = (int) $months)
            ->and($weeks = (int) $weeks)
            ->and($days = (int) $days)
            ->and($hours = (int) $hours)
            ->and($minutes = (int) $minutes)
            ->and($seconds = (int) $seconds)

            ->then
                ->assert('normally, 1 year = 12 months, trying with ' . $years . ' years and ' . $months . ' months')
                    ->array($this->testedInstance->inUnits('months'))
                        ->hasSize(1)
                        ->hasKey('months')
                        ->strictlyContains($years * 12 + $months)

                ->assert('trying with years (' . $years . ') and months (' . $months . ')')
                    ->array($this->testedInstance->inUnits('years', 'months'))
                        ->hasSize(2)
                        ->hasKey('years')
                        ->strictlyContains($years)
                        ->hasKey('months')
                        ->strictlyContains($months)

                ->assert('1 hour = 60 minutes, trying with ' . $hours . ' hours and ' . $minutes . ' minutes')
                    ->array($this->testedInstance->inUnits('minutes'))
                        ->hasSize(1)
                        ->hasKey('minutes')
                        ->strictlyContains($hours * 60 + $minutes)
                        ->isIdenticalTo($this->testedInstance->inUnits(array('minutes')))

                ->assert('full calc, ' . $days . ' days / ' . $minutes . ' minutes / ' . $months . ' months / ' . $seconds . ' seconds')
                    ->array($this->testedInstance->inUnits('days', 'minutes', 'months', 'seconds'))
                        ->hasSize(4)
                        ->hasKey('months')
                        ->strictlyContains($years * 12 + $months)
                        ->hasKey('days')
                        ->strictlyContains($weeks * 7 + $days)
                        ->hasKey('minutes')
                        ->strictlyContains($hours * 60 + $minutes)
                        ->hasKey('seconds')
                        ->strictlyContains($seconds)

                ->assert('giving arrays or strings does not matters')
                    ->array($this->testedInstance->inUnits(array('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds')))
                        ->isIdenticalTo($this->testedInstance->inUnits('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))

                ->assert('ignore twice unit')
                    ->array($this->testedInstance->inUnits('weeks', 'weeks'))
                        ->hasSize(1)
                        ->hasKey('weeks')
                        ->strictlyContains($weeks)

                ->assert('everything that is not an unit is ignored')
                    ->array($this->testedInstance->inUnits(uniqid()))
                        ->isEmpty

                ->assert('order is kept')
                    ->array($array1 = $this->testedInstance->inUnits($units = array('months', 'years')))
                        ->isEqualTo($array2 = $this->testedInstance->inUnits($reversed = array_reverse($units)))
                        ->isNotIdenticalTo($this->testedInstance->inUnits($reversed))
                    ->integer(current($array1))
                        ->isIdenticalTo(end($array2))
        ;
    }


    /**
     * @dataProvider unitsProvider
     */
    public function testYears($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($years += intval($months / 12))
            ->and($values = $this->testedInstance->inUnits('years'))

            ->then
                ->integer($this->testedInstance->years())
                    ->isIdenticalTo((int) $years)
                    ->isIdenticalTo($values['years'])

                ->integer($this->testedInstance->YEARS())
                    ->isIdenticalTo((int) $years)
                    ->isIdenticalTo($values['years'])

                ->integer($this->testedInstance->years)
                    ->isIdenticalTo((int) $years)
                    ->isIdenticalTo($values['years'])

                ->integer($this->testedInstance->YEARS)
                    ->isIdenticalTo((int) $years)
                    ->isIdenticalTo($values['years'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testMonths($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($months += $years * 12)
            ->and($values = $this->testedInstance->inUnits('months'))

            ->then
                ->integer($this->testedInstance->months())
                    ->isIdenticalTo((int) $months)
                    ->isIdenticalTo($values['months'])

                ->integer($this->testedInstance->MONTHS())
                    ->isIdenticalTo((int) $months)
                    ->isIdenticalTo($values['months'])

                ->integer($this->testedInstance->months)
                    ->isIdenticalTo((int) $months)
                    ->isIdenticalTo($values['months'])

                ->integer($this->testedInstance->MONTHS)
                    ->isIdenticalTo((int) $months)
                    ->isIdenticalTo($values['months'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testWeeks($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($weeks += intval($days / 7))
            ->and($values = $this->testedInstance->inUnits('weeks'))

            ->then
                ->integer($this->testedInstance->weeks())
                    ->isIdenticalTo((int) $weeks)
                    ->isIdenticalTo($values['weeks'])

                ->integer($this->testedInstance->WEEKS())
                    ->isIdenticalTo((int) $weeks)
                    ->isIdenticalTo($values['weeks'])

                ->integer($this->testedInstance->weeks)
                    ->isIdenticalTo((int) $weeks)
                    ->isIdenticalTo($values['weeks'])

                ->integer($this->testedInstance->WEEKS)
                    ->isIdenticalTo((int) $weeks)
                    ->isIdenticalTo($values['weeks'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testDays($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($days += $weeks * 7)
            ->and($values = $this->testedInstance->inUnits('days'))

            ->then
                ->integer($this->testedInstance->days())
                    ->isIdenticalTo((int) $days)
                    ->isIdenticalTo($values['days'])

                ->integer($this->testedInstance->DAYS())
                    ->isIdenticalTo((int) $days)
                    ->isIdenticalTo($values['days'])

                ->integer($this->testedInstance->days)
                    ->isIdenticalTo((int) $days)
                    ->isIdenticalTo($values['days'])

                ->integer($this->testedInstance->DAYS)
                    ->isIdenticalTo((int) $days)
                    ->isIdenticalTo($values['days'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testHours($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($hours += intval($minutes / 60))
            ->and($values = $this->testedInstance->inUnits('hours'))

            ->then
                ->integer($this->testedInstance->hours())
                    ->isIdenticalTo((int) $hours)
                    ->isIdenticalTo($values['hours'])

                ->integer($this->testedInstance->HOURS())
                    ->isIdenticalTo((int) $hours)
                    ->isIdenticalTo($values['hours'])

                ->integer($this->testedInstance->hours)
                    ->isIdenticalTo((int) $hours)
                    ->isIdenticalTo($values['hours'])

                ->integer($this->testedInstance->HOURS)
                    ->isIdenticalTo((int) $hours)
                    ->isIdenticalTo($values['hours'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testMinutes($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($minutes += $hours * 60)
            ->and($values = $this->testedInstance->inUnits('minutes'))

            ->then
                ->integer($this->testedInstance->minutes())
                    ->isIdenticalTo((int) $minutes)
                    ->isIdenticalTo($values['minutes'])

                ->integer($this->testedInstance->MINUTES())
                    ->isIdenticalTo((int) $minutes)
                    ->isIdenticalTo($values['minutes'])

                ->integer($this->testedInstance->minutes)
                    ->isIdenticalTo((int) $minutes)
                    ->isIdenticalTo($values['minutes'])

                ->integer($this->testedInstance->MINUTES)
                    ->isIdenticalTo((int) $minutes)
                    ->isIdenticalTo($values['minutes'])
        ;
    }

    /**
     * @dataProvider unitsProvider
     */
    public function testSeconds($years, $months, $weeks, $days, $hours, $minutes, $seconds)
    {
        $this
            ->given($args = compact('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'))
            ->and($this->newTestedInstance($args))

            ->if($values = $this->testedInstance->inUnits('seconds'))

            ->then
                ->integer($this->testedInstance->seconds())
                    ->isIdenticalTo((int) $seconds)
                    ->isIdenticalTo($values['seconds'])

                ->integer($this->testedInstance->SECONDS())
                    ->isIdenticalTo((int) $seconds)
                    ->isIdenticalTo($values['seconds'])

                ->integer($this->testedInstance->seconds)
                    ->isIdenticalTo((int) $seconds)
                    ->isIdenticalTo($values['seconds'])

                ->integer($this->testedInstance->SECONDS)
                    ->isIdenticalTo((int) $seconds)
                    ->isIdenticalTo($values['seconds'])
        ;
    }


    public function unitsProvider()
    {
        // $years, $months, $weeks, $days, $hours, $minutes, $seconds
        return array(
            array(5, 2, 0, -10, 1, 3, -5),
            array(0, 0, 0, 0, 0, 0, 3600),
            array(1, 0, 0, 0, 0, 0, 0),
            array(0, 0, 1, 0, 0, 0, 0),
            array(-1, -1, -1, -1, -1, -1, -1),
            array(rand(0, 100), rand(0, 100), rand(0, 100), rand(0, 100), rand(0, 100), rand(0, 100), rand(0, 100)),
        );
    }
}
