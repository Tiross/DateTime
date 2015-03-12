<?php

namespace DateTime\test\unit;

use atoum;

class InvalidTimeZoneException extends atoum
{
    public function testClass()
    {
        $this
            ->class('\DateTime\InvalidTimeZoneException')
                ->isSubclassOf('\InvalidArgumentException')
        ;
    }
}
