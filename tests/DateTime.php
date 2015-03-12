<?php

namespace Tiross\DateTime\test\unit;

class DateTime extends \atoum
{
    public function testClass()
    {
        $this
            ->class('\Tiross\DateTime\DateTime')
                ->isSubclassOf('\DateTime')
        ;
    }
}
