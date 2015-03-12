<?php

namespace Tiross\DateTime\Exception\tests\unit;

class LogicException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\Exception\LogicException')
                ->isSubclassOf('\LogicException')
        ;
    }
}
