<?php

namespace Apps\PHPfox_IM\Ajax;

class Ajax extends \Phpfox_Ajax
{
    public function toogleHosting()
    {
        storage()->del('im/host/status');
        storage()->set('im/host/status', strtolower($this->get('status')));
    }
}