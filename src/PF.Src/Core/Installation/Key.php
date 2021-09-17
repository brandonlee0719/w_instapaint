<?php

namespace Core\Installation;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SCP;
use phpseclib\Net\SFTP;

/**
 * Class Key
 *
 * @author  Neil J. <neil@phpfox.com>
 * @package Core\Installation
 */
class Key extends Vfs
{
    /**
     * @var SFTP
     */
    private $_sftp;

    /**
     * @var RSA
     */
    private $_key;

    /**
     * @var SCP
     */
    private $_scp;

    /**
     * @var string
     */
    private $_passphrase = '';

    /**
     * Ssh2 constructor.
     *
     * @param $param
     */
    public function __construct($param)
    {
        if (isset($param['port']) && ($param['port'] != 'auto')) {
            $this->port = $param['port'];
        } else {
            $this->port = 22;
        }

        if (isset($param['host'])) {
            $this->host = $param['host'];
        } else {
            $this->host = 'localhost';
        }

        if (isset($param['user'])) {
            $this->user = $param['user'];
        } else {
            $this->error = _p('Please set user name');
        }

        if (isset($param['key'])) {
            $this->pass = $param['key'];
        } else {
            $this->error = _p('Please upload a key');
        }

        if (isset($param['passphrase'])) {
            $this->_passphrase = $param['passphrase'];
        }

        $this->_sftp = new SFTP($this->host, $this->port);
        $this->_key = new RSA();
        if (!empty($this->_passphrase)) {
            $this->_key->setPassword($this->_passphrase);
        }
        $this->_key->loadKey($param['key']);
    }


    /**
     * @return array
     */
    public function verify()
    {
        if ($this->error) {
            return [false, $this->error];
        } else {
            $this->verified = true;
            return [true, true];
        }
    }

    /**
     * @return bool
     */
    public function connect()
    {
        if (!$this->_sftp->login($this->user, $this->_key)) {
            $this->error = "Can't to login";
            return false;
        }
        return true;
    }


    /**
     * @param string $file_path
     * @param string $to_file_path
     *
     * @return array|bool
     */
    public function up($file_path, $to_file_path = null)
    {
        if (null == $to_file_path) {
            $to_file_path = $file_path;
        }

        $bReturn = true;
        $fromPath = realpath($this->from_path . PHPFOX_DS . $file_path);
        $toPath = rtrim($this->to_path, '/') . '/' . $to_file_path;
        if (!file_exists($fromPath)) {
            return false;
        }
        $create_path = dirname($toPath);

        /*Create folder structure*/
        if (!$this->_sftp->is_dir($create_path)) {
            $this->_sftp->mkdir($create_path, 0777, true);
        }
        if ($this->_sftp->put($toPath, $fromPath, SCP::SOURCE_LOCAL_FILE)) {
            $msg = _p("Successfully copied ") . $file_path;
        } else {
            $bReturn = false;
            $msg = _p("There was a problem while copied ") . $file_path;
        }

        return [$bReturn, $msg];
    }

    /**
     * @param $path
     *
     * @return null
     */
    public function deleteDir($path)
    {
        $files = $this->_sftp->nlist($path);
        if (count($files)) {
            foreach ($files as $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $newPath = $path . PHPFOX_DS . $file;
                if ($this->_sftp->is_dir($newPath)) {
                    $this->deleteDir($newPath);
                } else {
                    $this->deleteFile($newPath);
                }
            }
        }
        $this->deleteSingFolder($path);
        return true;
    }

    /**
     * @param $file_path
     *
     * @return array
     */
    public function deleteFile($file_path)
    {
        $bReturn = true;
        if (isset($this->to_path)) {
            $toPath = $this->to_path . PHPFOX_DS . $file_path;
        } else {
            $toPath = $file_path;
        }
        /*Delete file on server*/
        if ($this->_sftp->delete($toPath)) {
            $msg = _p("Successfully deleted") . $file_path;
        } else {
            $bReturn = false;
            $msg = _p("There was a problem while deleted") . $file_path;
        }
        //delete folder if empty
        $this->deleteSingFolder($file_path);

        return [$bReturn, $msg];
    }

    /**
     * @param $filePath
     *
     * @return array
     */
    public function deleteSingFolder($filePath)
    {
        $bReturn = true;
        if (isset($this->to_path)) {
            $toPath = dirname($this->to_path . PHPFOX_DS . $filePath);
        } else {
            $toPath = $filePath;
        }
        /*Delete file on server*/
        if ($this->_sftp->rmdir($toPath)) {
            $msg = _p("Successfully deleting ") . $filePath;
        } else {
            //If folder is not empty, we will see this message. Please consider to show it to message.
            $bReturn = false;
            $msg = _p("There was a problem while deleting ") . $filePath;
        }

        return [$bReturn, $msg];
    }
}