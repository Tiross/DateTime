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

    /** @php 5.5 */
    public function test__construct_After55()
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

    public function testYear()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('Y', $timestamp) * 1)
            ->and($inPast = $actual - 10)
            ->and($inFuture = $actual + 10)

            ->if($this->newTestedInstance('@' . $timestamp))

            ->then
                ->integer($this->testedInstance->year($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->year())
                    ->isIdenticalTo($this->testedInstance->year)
                    ->isIdenticalTo($this->testedInstance->YEAR)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->year($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->year())
                    ->isIdenticalTo($this->testedInstance->year)
                    ->isIdenticalTo($this->testedInstance->YEAR)
                    ->isIdenticalTo($inFuture)
        ;
    }

    public function testMonth()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('m', $timestamp) * 1)
            ->and($inPast = $actual - 1)
            ->and($inFuture = $actual + 1)

            ->if($this->newTestedInstance('@' . $timestamp))

            ->then
                ->integer($this->testedInstance->month($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->month())
                    ->isIdenticalTo($this->testedInstance->month)
                    ->isIdenticalTo($this->testedInstance->MONTH)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->month($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->month())
                    ->isIdenticalTo($this->testedInstance->month)
                    ->isIdenticalTo($this->testedInstance->MONTH)
                    ->isIdenticalTo($inFuture)
        ;
    }

    public function testDay()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('d', $timestamp) * 1)
            ->and($inPast = $actual - 1)
            ->and($inFuture = $actual + 1)

            ->if($this->newTestedInstance('@' . $timestamp))

            ->then
                ->integer($this->testedInstance->day($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->day())
                    ->isIdenticalTo($this->testedInstance->day)
                    ->isIdenticalTo($this->testedInstance->DAY)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->day($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->day())
                    ->isIdenticalTo($this->testedInstance->day)
                    ->isIdenticalTo($this->testedInstance->DAY)
                    ->isIdenticalTo($inFuture)
        ;
    }

    public function testHour()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('H', $timestamp) * 1)
            ->and($inPast = $actual == 0 ? 23 : $actual - 1)
            ->and($inFuture = $actual >= 23 ? 0 : $actual + 1)

            ->if($this->newTestedInstance('@' . $timestamp, 'UTC'))

            ->then
                ->integer($this->testedInstance->hour($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->hour())
                    ->isIdenticalTo($this->testedInstance->hour)
                    ->isIdenticalTo($this->testedInstance->HOUR)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->hour($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->hour())
                    ->isIdenticalTo($this->testedInstance->hour)
                    ->isIdenticalTo($this->testedInstance->HOUR)
                    ->isIdenticalTo($inFuture)
        ;
    }

    public function testMinute()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('i', $timestamp) * 1)
            ->and($inPast = $actual == 0 ? 59 : $actual - 1)
            ->and($inFuture = $actual >= 60 ? 0 : $actual + 1)

            ->if($this->newTestedInstance('@' . $timestamp))

            ->then
                ->integer($this->testedInstance->minute($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->minute())
                    ->isIdenticalTo($this->testedInstance->minute)
                    ->isIdenticalTo($this->testedInstance->MINUTE)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->minute($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->minute())
                    ->isIdenticalTo($this->testedInstance->minute)
                    ->isIdenticalTo($this->testedInstance->MINUTE)
                    ->isIdenticalTo($inFuture)
        ;
    }

    public function testSecond()
    {
        $this
            ->given($timestamp = time())
            ->and($actual = date('s', $timestamp) * 1)
            ->and($inPast = $actual == 0 ? 59 : $actual - 1)
            ->and($inFuture = $actual >= 60 ? 0 : $actual + 1)

            ->if($this->newTestedInstance('@' . $timestamp))

            ->then
                ->integer($this->testedInstance->second($inPast))
                    ->isIdenticalTo($actual)

                ->integer($this->testedInstance->second())
                    ->isIdenticalTo($this->testedInstance->second)
                    ->isIdenticalTo($this->testedInstance->SECOND)
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->second($inFuture))
                    ->isIdenticalTo($inPast)

                ->integer($this->testedInstance->second())
                    ->isIdenticalTo($this->testedInstance->second)
                    ->isIdenticalTo($this->testedInstance->SECOND)
                    ->isIdenticalTo($inFuture)
        ;
    }

    /** @dataProvider dateProvider */
    public function testYmd($ymd, $dmy)
    {
        $this
            ->if($this->newTestedInstance($ymd))
            ->then
                ->string($this->testedInstance->ymd())
                    ->isIdenticalTo($this->testedInstance->ymd)
                    ->isIdenticalTo($this->testedInstance->YmD)
                    ->isIdenticalTo($this->testedInstance->format('Y-m-d'))
                    ->isIdenticalTo($ymd)

                ->string($this->testedInstance->ymd(' '))
                    ->isIdenticalTo(str_replace('-', ' ', $ymd))
        ;
    }

    /** @dataProvider dateProvider */
    public function testDmy($ymd, $dmy)
    {
        $this
            ->if($this->newTestedInstance($ymd))
            ->then
                ->string($this->testedInstance->dmy())
                    ->isIdenticalTo($this->testedInstance->dmy)
                    ->isIdenticalTo($this->testedInstance->DmY)
                    ->isIdenticalTo($this->testedInstance->format('d/m/Y'))
                    ->isIdenticalTo($dmy)

                ->string($this->testedInstance->dmy(' '))
                    ->isIdenticalTo(str_replace('/', ' ', $dmy))
        ;
    }

    /** @dataProvider timeProvider */
    public function testHms($time)
    {
        $this
            ->if($this->newTestedInstance($time))
            ->then
                ->string($this->testedInstance->hms())
                    ->isIdenticalTo($this->testedInstance->hms)
                    ->isIdenticalTo($this->testedInstance->HmS)
                    ->isIdenticalTo($this->testedInstance->format('H:i:s'))

                ->string($this->testedInstance->hms(' '))
                    ->isIdenticalTo(str_replace(':', ' ', $time))
        ;
    }

    public function dateProvider()
    {
        // $ymd, $dmy
        return array(
            array('2015-01-01', '01/01/2015'),
            array('1999-05-12', '12/05/1999'),
            array('1970-01-01', '01/01/1970'),
        );
    }

    public function timeProvider()
    {
        // $time
        return array(
            array('12:34:56'),
            array('01:01:01'),
            array('23:59:59'),
        );
    }
}
