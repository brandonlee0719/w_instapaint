<?php

namespace Core\Installation;

class FileSystem extends Vfs
{
    /**
     * FileSystem constructor.
     *
     * @param null $param
     */
    public function __construct($param = null)
    {

    }

    /**
     * @return bool
     */
    public function verify()
    {
        $this->verified = true;

        return true;
    }

    /**
     * @return bool
     */
    public function connect()
    {
        return true;
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

        // skips package.json, checksum.json
        if (in_array($file_path, ['package.json', 'checksum.json'])) {
            return [true, null];
        }

        $fromPath = $this->from_path . PHPFOX_DS . $file_path;
        $toPath = $this->to_path . PHPFOX_DS . $to_file_path;
        $base = dirname($toPath);

        if (!is_dir($base)) {
            mkdir($base, 0755, true);
            chmod($base, 0755);
        }

        // check file exists
        if (file_exists($toPath)) {
            if (!@unlink($toPath)) {
                throw new \RuntimeException(sprintf('Can not open "%s" to overwrite', $toPath));
            }
        }

        if (@copy($fromPath, $toPath)) {
            return [true, $this->error];
        } else {
            throw new \InvalidArgumentException(_p('Can not write to file ') . $toPath);
        }
    }

    public function deleteDir($path)
    {
        $realPath = realpath($path);
        if (!is_dir($realPath)) {
            return false;
        }

        $objects = scandir($path);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($path . PHPFOX_DS . $object)) {
                    $this->deleteDir($path . PHPFOX_DS . $object);
                } else {
                    @unlink($path . PHPFOX_DS . $object);
                }
            }
        }
        return @rmdir($path);
    }

    public function deleteFile($file_path, $check_dir = false)
    {
        if (isset($this->to_path)) {
            $toPath = $this->to_path . PHPFOX_DS . $file_path;
        } else {
            $toPath = $file_path;
        }
        if (!file_exists($toPath)) {
            return false;
        }

        if ($check_dir && is_dir($toPath)) {
            return $this->deleteDir($toPath);
        }

        return (unlink($toPath)) ? true : false;
    }

    public function deleteSingFolder($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        return rmdir($path) ? true : false;
    }
}