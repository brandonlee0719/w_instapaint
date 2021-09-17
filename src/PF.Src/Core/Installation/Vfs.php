<?php

namespace Core\Installation;

/**
 * Class Vfs
 *
 * @package Core\Installation
 */
abstract class Vfs
{

    /**
     * @var
     */
    protected $listFiles;
    /**
     * @var
     */
    protected $to_path = null;
    /**
     * @var
     */
    protected $from_path = null;
    /**
     * @var
     */
    protected $host = 'localhost';
    /**
     * @var
     */
    protected $user = 'root';
    /**
     * @var
     */
    protected $pass = '';
    /**
     * @var
     */
    protected $port = 22;
    /**
     * @var bool
     */
    protected $error = false;
    /**
     * @var bool
     */
    protected $verified = false;

    /**
     * @var string
     */
    protected $root_path = '';

    /**
     * @var string
     */
    protected $folderPermission = 0755;
    /**
     * @var string
     */
    protected $filePermission = 0644;

    /**
     * @return mixed
     */
    abstract public function verify();

    /**
     * @return mixed
     */
    abstract public function connect();

    /**
     * @param        $file_path
     * @param string $to_file_path
     *
     * @return array
     */
    abstract public function up($file_path, $to_file_path = null);

    /**
     * @param $file_path
     *
     * @return mixed
     */
    abstract public function deleteFile($file_path);

    /**
     * @param $path
     */
    abstract public function deleteDir($path);

    /**
     * @param $path
     *
     * @return mixed
     */
    abstract public function deleteSingFolder($path);


    /**
     * @param $param
     *
     * @return $this
     * @return Vfs
     */
    public function setFile($param)
    {
        $this->listFiles = $param;
        return $this;
    }

    /**
     * @param $path
     *
     * @return Vfs
     */
    public function setToPath($path)
    {
        $this->to_path = $this->correctFtpPath($path);
        return $this;
    }

    /**
     * @param $path
     *
     * @return Vfs
     */
    public function setFromPath($path)
    {
        $this->from_path = $path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        if ($this->verified || $this->verify()) {
            foreach ($this->listFiles as $name => $value) {
                if (is_int($name)) {
                    $this->up($value);
                } else {
                    $this->up($name, $value);
                }

            }
        } else {
            \Phpfox_Error::set(_p('There were some issues when extract files'));
        }
    }

    protected function correctFtpPath($path)
    {
        if (empty($this->root_path) || $this->root_path == '/') {
            return $path;
        }
        return str_replace($this->root_path, '', $path);
    }
}