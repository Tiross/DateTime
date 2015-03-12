<?php

namespace Tiross\DateTime\Exception\test\unit;

class InvalidTimeZoneException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\Exception\InvalidTimeZoneException')
                ->isSubclassOf('\InvalidArgumentException')
        ;
    }
}
