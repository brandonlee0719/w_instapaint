<?php

namespace Core\Installation;

/**
 * Class Ssh2
 *
 * @deprecated will be removed from 4.6.0
 * @package    Core\Installation
 */
class Ssh2 extends Vfs
{
    /**
     * @var
     */
    private $connect_id;

    /**
     * @var
     */
    private $ftp_id;

    /**
     * @var
     */
    private $connect_status;

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

        if (isset($param['pass'])) {
            $this->pass = $param['pass'];
        } else {
            $this->error = _p('Please set password');
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
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

            return [true, $this->connect_status];
        }
    }

    /**
     * @return bool
     */
    public function connect()
    {
        if (function_exists('ssh2_connect')) {
            $this->connect_id = ssh2_connect($this->host, $this->port);
            if ($this->connect_status = ssh2_auth_password($this->connect_id, $this->user, $this->pass)) {
                $this->ftp_id = ssh2_sftp($this->connect_id);

                return true;
            } else {
                return false;
            }
        } else {
            $this->error[] = _p('Your server don\'t support ssh2 function');

            return false;
        }
    }

    /**
     * close connection to ftp server
     */
    private function close()
    {
        if (isset($this->connect_id)) {
            unset($this->connect_id);
        }
    }


    /**
     * @param string $file_path
     * @param string $to_file_path
     *
     * @return array
     */
    public function up($file_path, $to_file_path = null)
    {
        if (null == $to_file_path) {
            $to_file_path = $file_path;
        }

        $bReturn = true;
        $fromPath = realpath($this->from_path . PHPFOX_DS . $file_path);
        $toPath = $this->to_path . PHPFOX_DS . $to_file_path;
        if (!file_exists($fromPath)) {
            return false;
        }
        $create_path = dirname($toPath);

        /*Create folder structure*/
        ssh2_sftp_mkdir($this->ftp_id, $create_path, $this->folderPermission, true);
        if (ssh2_scp_send($this->connect_id, $fromPath, $toPath, $this->filePermission)) {
            $msg = _p("Successfully copied ") . $file_path;
        } else {
            $bReturn = false;
            $msg = _p("There was a problem while copied ") . $file_path;
        }

        return [$bReturn, $msg];
    }

    /**
     * @param $path
     */
    public function deleteDir($path)
    {
        $files = scandir('ssh2.sftp://' . $this->ftp_id . $path);
        if (count($files)) {
            foreach ($files as $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $newPath = $path . PHPFOX_DS . $file;
                if (is_dir('ssh2.sftp://' . $this->ftp_id . $newPath)) {
                    $this->deleteDir($newPath);
                } else {
                    $this->deleteFile($newPath);
                }
            }
        }
        $this->deleteSingFolder($path);
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
        if (ssh2_sftp_unlink($this->ftp_id, $toPath)) {
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
     * @return bool
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
        if (ssh2_sftp_rmdir($this->ftp_id, $toPath)) {
            $msg = _p("Successfully deleting ") . $filePath;
        } else {
            //If folder is not empty, we will see this message. Please consider to show it to message.
            $bReturn = false;
            $msg = _p("There was a problem while deleting ") . $filePath;
        }

        return [$bReturn, $msg];
    }
}