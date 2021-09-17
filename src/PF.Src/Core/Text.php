<?php

namespace Core;

class Text
{
    public function parse($str)
    {
        return new Text\Parse($str);
    }

    public function clean($str, $length = null)
    {
        return \Phpfox_Parse_Input::instance()->clean($str, $length);
    }
}