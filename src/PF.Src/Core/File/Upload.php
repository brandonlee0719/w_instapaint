<?php

namespace Core\File;

class Upload
{
    private $_name;
    private $_file;
    private $_ext;

    public function __construct($name)
    {
        $this->_name = $name;
        $this->_file = $_FILES[$name];
        $this->_ext = \Phpfox_File::instance()->extension($this->_file['name']);
    }

    public function ext()
    {
        return $this->_ext;
    }

    public function move($destination)
    {
        $path = PHPFOX_DIR_FILE . $destination;

        if (!is_dir($path)) {
            \Phpfox_File::instance()->mkdir(dirname($path), true);
        }

        if (!@move_uploaded_file($this->_file['tmp_name'], $path)) {
            error('Unable to move file to: ' . $path);
        }
    }
}