<?php

namespace DateTime\test\unit;

class DateTime extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\DateTime\DateTime')
                ->isSubclassOf('\DateTime')
        ;
    }

    public function test__construct()
    {
        $this
            ->given($controller = new \atoum\mock\controller())
            ->and($controller->__construct = function() {var_dump(func_get_args()); var_dump(get_class($this));})

            ->assert('string argument / no tz')
                ->if($date = '2015-01-01')
                ->and($tz = null)
                ->then
                    ->mock(new \mock\DateTime\DateTime($date, $tz, $controller))
                        ->call('__construct')
                            ->withIdenticalArguments($date, $tz)
                            ->once

            ->assert('string argument / string tz')
                ->if($date = '2015-01-01')
                ->and($tz = 'Europe/Paris')
                ->and(new \mock\DateTime\DateTime($date, $tz, $controller))
->stop
                ->then
                    ->mock(new \mock\DateTime\DateTime($date, $tz, $controller))
                        ->call('__construct')
                            ->withIdenticalArguments($date, new \DateTime\TimeZone($tz))
                            ->once

            ->assert('string argument / dtz tz')
                ->if($date = '2015-01-01')
                ->and($tz = new \DateTimeZone('Europe/Paris'))
                ->then
                    ->mock(new \mock\DateTime\DateTime($date, $tz, $controller))
                        ->call('__construct')
                            ->withIdenticalArguments($date, $tz)
                            ->once

            ->assert('empty array argument / no tz')
                ->mock(new \mock\DateTime\DateTime(array(), null, $controller))
                    ->call('__construct')
                        ->withIdenticalArguments(null, null)
                        ->once

            ->assert('array argument / no tz')
                ->if($string = '2015-01-01')
                ->and($tmp = explode('-', $string))
                ->and($date = array_combine(array('year', 'month', 'day'), $tmp))
                ->and($tz = null)
                ->then
                    ->mock(new \mock\DateTime\DateTime($date, $tz, $controller))
                        ->call('__construct')
                            ->withIdenticalArguments($string, $tz)
                            ->once
        ;
    }

    public function constructWithoutTimeZoneProvider()
    {
        return array(
            array(null, null),
            array(array(), null),

            array('2015-01-01', '2015-01-01'),
            array('2015-01-01 20:08', '2015-01-01 20:08'),
            array('2015-01-01 20:08:32', '2015-01-01 20:08:32'),

            array('2015-01-01T', '2015-01-01'),
            array('2015-01-01T20:08', '2015-01-01 20:08'),
            array('2015-01-01T20:08:32', '2015-01-01 20:08:32'),

            array(array('year' => 2015, 'month' => 1, 'day' => 1), '2015-01-01'),
            array(array('year' => 2015, 'month' => 1, 'day' => 1, 'hour' => 20, 'minute' => 8), '2015-01-01 20:08:32'),
        );
    }
}
