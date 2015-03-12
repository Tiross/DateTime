<?php

namespace Tiross\DateTime\Exception\test\unit;

class InvalidDurationException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\Exception\InvalidDurationException')
                ->isSubclassOf('\InvalidArgumentException')
        ;
    }
}
