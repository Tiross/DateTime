<?php

namespace Tiross\DateTime\test\unit;

use Tiross\DateTime\DateTime as testedClass;
use Tiross\DateTime\TimeZone;
use Tiross\DateTime\Duration;
use DateInterval;

class DateTime extends \atoum
{
    public function setUp()
    {
        date_default_timezone_set('UTC');
    }

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

                ->assert('New instance from array with only date and DateTimeZone as parameter')
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
            ->if($dt = new testedClass)
            ->then
                ->dateTime(testedClass::now())
                    ->isEqualTo($dt)
        ;
    }

    public function testYear()
    {
        $this
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = rand(1, 10))
            ->and($actual = (int) date('Y', $timestamp))
            ->and($inPast = (int) date('Y', mktime($hour, $minute, $second, $month, $day, $year - $diff)))
            ->and($inFuture = (int) date('Y', mktime($hour, $minute, $second, $month, $day, $year + $diff)))

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
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = rand(1, 5))
            ->and($actual = (int) date('m', $timestamp))
            ->and($inPast = (int) date('m', mktime($hour, $minute, $second, $month - $diff, $day, $year)))
            ->and($inFuture = (int) date('m', mktime($hour, $minute, $second, $month + $diff, $day, $year)))

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
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = rand(1, 4))
            ->and($actual = (int) date('d', $timestamp))
            ->and($inPast = (int) date('d', mktime($hour, $minute, $second, $month, $day - $diff, $year)))
            ->and($inFuture = (int) date('d', mktime($hour, $minute, $second, $month, $day + $diff, $year)))

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
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = rand(1, 6))
            ->and($actual = (int) date('G', $timestamp))
            ->and($inPast = (int) date('G', mktime($hour - $diff, $minute, $second, $month, $day, $year)))
            ->and($inFuture = (int) date('G', mktime($hour + $diff, $minute, $second, $month, $day, $year)))

            ->if($this->newTestedInstance('@' . $timestamp))

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
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = rand(1, 10))
            ->and($actual = (int) date('i', $timestamp))
            ->and($inPast = (int) date('i', mktime($hour, $minute - $diff, $second, $month, $day, $year)))
            ->and($inFuture = (int) date('i', mktime($hour, $minute + $diff, $second, $month, $day, $year)))

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
            ->given($year = 2006, $month = 5, $day = 4)
            ->and($hour = 12, $minute = 34, $second = 56)

            ->and($timestamp = mktime($hour, $minute, $second, $month, $day, $year))

            ->and($diff = 1)
            ->and($actual = (int) date('s', $timestamp))
            ->and($inPast = (int) date('s', mktime($hour, $minute, $second - $diff, $month, $day, $year)))
            ->and($inFuture = (int) date('s', mktime($hour, $minute, $second + $diff, $month, $day, $year)))

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

    /** @dataProvider durationProvider */
    public function testAddDuration($start, $duration, $end)
    {
        $this
            ->given($dur = new Duration($duration))
            ->and($expected = $this->newTestedInstance($end)->clone())
            ->if($this->newTestedInstance($start))
            ->then
                ->dateTime($this->testedInstance->addDuration($dur))
                    ->isEqualTo($expected)
        ;
    }

    /** @dataProvider intervalProvider */
    public function testAddInterval($start, $duration, $end)
    {
        $this
            ->given($dur = new DateInterval($duration))
            ->and($expected = $this->newTestedInstance($end)->clone())
            ->if($this->newTestedInstance($start))
            ->then
                ->dateTime($this->testedInstance->addInterval($dur))
                    ->isEqualTo($expected)
        ;
    }

    /** @dataProvider intervalProvider */
    public function testAdd($start, $duration, $end)
    {
        $this
            ->given($dur = new Duration($duration))
            ->and($interval = new DateInterval($duration))

            ->if($expected = $this->newTestedInstance($end)->clone())

            ->then
                ->assert('doing math with Duration')
                    ->dateTime($this->newTestedInstance($start)->add($dur))
                        ->isEqualTo($expected)

                ->assert('doing math with DateInterval')
                    ->dateTime($this->newTestedInstance($start)->add($interval))
                        ->isEqualTo($expected)

                ->assert('doing math with Duration constructor parameters')
                    ->dateTime($this->newTestedInstance($start)->add($duration))
                        ->isEqualTo($expected)
        ;
    }

    /** @dataProvider durationProvider */
    public function testSubDuration($end, $duration, $start)
    {
        $this
            ->given($dur = new Duration($duration))
            ->and($expected = $this->newTestedInstance($end)->clone())
            ->if($this->newTestedInstance($start))
            ->then
                ->dateTime($a = $this->testedInstance->subDuration($dur))
                    ->isEqualTo($expected)
        ;
    }

    /** @dataProvider intervalProvider */
    public function testSubInterval($end, $duration, $start)
    {
        $this
            ->given($dur = new DateInterval($duration))
            ->and($expected = $this->newTestedInstance($end)->clone())
            ->if($this->newTestedInstance($start))
            ->then
                ->dateTime($this->testedInstance->subInterval($dur))
                    ->isEqualTo($expected)
        ;
    }

    /** @dataProvider intervalProvider */
    public function testSub($end, $duration, $start)
    {
        $this
            ->given($dur = new Duration($duration))
            ->and($interval = new DateInterval($duration))

            ->if($expected = $this->newTestedInstance($end)->clone())

            ->then
                ->assert('doing math with Duration')
                    ->dateTime($this->newTestedInstance($start)->sub($dur))
                        ->isEqualTo($expected)

                ->assert('doing math with DateInterval')
                    ->dateTime($this->newTestedInstance($start)->sub($interval))
                        ->isEqualTo($expected)

                ->assert('doing math with Duration constructor parameters')
                    ->dateTime($this->newTestedInstance($start)->sub($duration))
                        ->isEqualTo($expected)
        ;
    }

    public function intervalProvider()
    {
        // $start, $duration, $end
        return array(
            array('2015-01-01T12:34:56Z', 'P1D', '2015-01-02T12:34:56Z'),
            array('2015-01-01T12:34:56Z', 'P1M', '2015-02-01T12:34:56Z'),
            array('2015-01-01T12:34:56Z', 'P1Y', '2016-01-01T12:34:56Z'),
            array('2015-01-01T12:34:56Z', 'PT4S', '2015-01-01T12:35:00Z'),
            array('2015-01-01T12:34:56Z', 'P0D', '2015-01-01T12:34:56Z'),
            array('2015-01-01T12:34:56Z', 'P1DT5M4S', '2015-01-02T12:40:00Z'),
        );
    }

    public function durationProvider()
    {
        // $start, $duration, $end

        $array = $this->intervalProvider();

        $array[] = array('2015-01-10T12:34:56Z', array('weeks' => 1, 'days' => -1), '2015-01-16T12:34:56Z');
        $array[] = array('2015-01-02T12:34:56Z', array('days' => -1), '2015-01-01T12:34:56Z');

        return $array;
    }

    /** @dataProvider durationProvider */
    public function testDiff($start, $duration, $end)
    {
        $this
            ->given($a = $this->newTestedInstance($start))
            ->and($result = new Duration($duration))

            ->if($b = $this->newTestedInstance($end))
            ->then
                ->object($a->diff($b))
                    ->isInstanceOf('\Tiross\DateTime\Duration')
                    ->isEqualTo($result)

                ->object($b->diff($a))
                    ->isEqualTo($result->clone()->inverse())

                ->object($b->diff($a, true))
                    ->isEqualTo($result->clone()->absolute())

            ->if($b = new \DateTime($end))
            ->then
                ->object($a->diff($b))
                    ->isInstanceOf('\Tiross\DateTime\Duration')
                    ->isEqualTo($result)

            ->if($b = new \stdClass)
            ->and($message = 'First argument must be an instance of \DateTime, instance of %s given')
            ->then
                ->exception(function () use ($a, $b) {
                    $a->diff($b);
                })
                    ->isInstanceOf('\Tiross\DateTime\Exception\LogicException')
                    ->hasCode(106)
                    ->hasMessage(sprintf($message, get_class($b)))
        ;
    }

    /** @dataProvider timezoneProvider */
    public function testGetOffset($timezone)
    {
        $this
            ->if($this->newTestedInstance(null, $timezone))
            ->then
                ->assert('Test with ' . $timezone)
                    ->object($this->testedInstance->getOffset())
                        ->isInstanceOf('\Tiross\DateTime\Duration')
                        ->isEqualTo($this->testedInstance->getTimezone()->getOffset())

                    ->object($this->testedInstance->getTimezone()->getOffset())
                        ->isEqualTo($this->testedInstance->getOffset)
                        ->isEqualTo($this->testedInstance->GETOFFSET)
        ;
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
}
