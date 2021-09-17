<?php

namespace Core\App\Install\Phrase;

/**
 * Class Phrase
 *
 * @author  Neil
 * @version 4.5.0
 * @package Core\App\Install\Phrase
 */
class Phrase
{
    private $_phrase = [];

    /**
     * @param string       $var_name
     * @param string|array $text
     */
    public function addPhrase($var_name, $text = '')
    {
        if (!empty($text)) {
            $this->_phrase[$var_name] = $text;
        } else {
            $this->_phrase['app_' . md5($var_name)] = $var_name;
        }
    }

    public function all()
    {
        return $this->_phrase;
    }

    public function isPhrase($var_name)
    {
        if (isset($this->_phrase[$var_name])) {
            return true;
        } else {
            return false;
        }
    }

    public function getPhrase($var_name)
    {
        if ($this->isPhrase($var_name)) {
            return $this->_phrase[$var_name];
        } else {
            return false;
        }
    }
}