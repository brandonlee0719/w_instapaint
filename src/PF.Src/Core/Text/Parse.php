<?php

namespace Core\Text;

class Parse
{
    private $_str;
    private $_tags = [];

    public function __construct($str)
    {
        $this->_str = $str;

        preg_match_all("/(#\w+)/iu", $this->_str, $tags);
        if (isset($tags[0])) {
            foreach ($tags[0] as $tag) {
                $tag = trim($tag);

                $this->_tags[] = strip_tags(str_replace('#', '', $tag));
            }
        }
    }

    public function text()
    {
        return text()->clean($this->_str);
    }

    public function tags()
    {
        return $this->_tags;
    }
}