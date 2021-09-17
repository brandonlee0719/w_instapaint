<?php

namespace Core;

class Moment
{
    public function now()
    {
        return PHPFOX_TIME;
    }

    public function toString($seconds)
    {
        return \Phpfox_Date::instance()->convertTime($seconds);
    }

    public function __toString()
    {
        return '' . $this->now() . '';
    }
}