<?php

namespace Apps\Core_Pages\Service;


class BrowseTest extends \PHPUnit_Framework_TestCase
{
    public function test_getFacade()
    {
        $obj = new Browse();
        $this->assertSame(true, $obj->getFacade() instanceof Facade);
    }
}
