<?php

namespace Tiross\DateTime\test\unit;

class InvalidTimeZoneException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\InvalidTimeZoneException')
                ->isSubclassOf('\InvalidArgumentException')
        ;
    }
}
