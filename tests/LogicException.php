<?php

namespace Tiross\DateTime\tests\unit;

class LogicException extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\LogicException')
                ->isSubclassOf('\LogicException')
        ;
    }
}
