<?php

namespace Tiross\DateTime\Exception\test\unit;

class InvalidDateTimeException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\Exception\InvalidDateTimeException')
                ->isSubclassOf('\InvalidArgumentException')
        ;
    }
}
