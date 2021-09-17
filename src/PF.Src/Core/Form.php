<?php

namespace Core;

class Form
{
    public function assign($keys)
    {
        \Phpfox_Template::instance()->assign('aForms', $keys);
    }
}