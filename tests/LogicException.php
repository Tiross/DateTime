<?php

namespace DateTime\tests\unit;

use atoum;

class LogicException extends atoum
{
    public function testClass()
    {
        $this
            ->class('\DateTime\LogicException')
                ->isSubclassOf('\LogicException')
        ;
    }
}
