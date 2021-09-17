<?php

namespace Core\Installation;

use Core\Db;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class FileHelper
{
    /**
     * @var array
     */
    private $excludes = ['_static_', 'app.lock', 'composer.phar', '.git', '.idea'];

    /**
     * @var string
     */
    private $rootPath;

    /**
     * Get root path parents
     *
     * @return string
     */
    public function getRootPath()
    {
        if (null == $this->rootPath) {
            $this->rootPath = realpath(dirname(PHPFOX_DIR));
        }

        return $this->rootPath;
    }

    /**
     * @param string $targetZipFilename
     * @param array  $paths
     * @param array  $packageInformation
     *
     * @return array
     */
    public function exportTheme($targetZipFilename, $paths, $packageInformation = [])
    {

        if (!is_dir(dirname($targetZipFilename))) {
            mkdir(dirname($targetZipFilename), 0777, true);
        }

        $db = new Db();

        $flavors = $db->select('*')
            ->from(':theme_style')
            ->where(['theme_id' => $packageInformation['theme_id']])
            ->all();

        $packageInformation['flavors'] = [];

        foreach ($flavors as $flavor) {
            $packageInformation['flavors'][$flavor['folder']] = $flavor['name'];
        }

        $checksumInformation = [];

        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (file_exists($targetZipFilename)) {
            if (!@unlink($targetZipFilename)) {
                exit(sprintf('Unable write to "%s"', $targetZipFilename));
            }
        }

        $zipArchive = new \ZipArchive();
        $zipArchive->open($targetZipFilename, \ZipArchive::CREATE);

        $result = [];
        foreach ($paths as $path) {

            $path = realpath($path);

            if (is_file($path)) {

                $local = $this->normalizeFileName($path);
                $zipArchive->addFile($path, $local);
                $checksumInformation[] = $local;

                continue;
            }


            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), null);
            foreach ($iterator as $fileInfo) {

                $pathname = $fileInfo->getPathName();

                $local = $this->normalizeFileName($pathname);

                if (!$local) {
                    continue;
                }

                if ($fileInfo->isDir()) {
                    $zipArchive->addEmptyDir($local);
                } else {
                    $zipArchive->addFile($pathname, $local);
                }
                $checksumInformation[] = $local;
            }
        }
        $zipArchive->addFromString('checksum.json', json_encode($checksumInformation, JSON_PRETTY_PRINT));
        $zipArchive->addFromString('package.json', json_encode($packageInformation, JSON_PRETTY_PRINT));

        $zipArchive->close();

        return $result;
    }

    /**
     * Normalize filename: Directory => "/", strip parent of "PF.Base"
     *
     * @param $filename
     *
     * @return string
     */
    private function normalizeFileName($filename)
    {
        $path = substr($filename, strlen($this->getRootPath()));
        $path = trim($path, DIRECTORY_SEPARATOR);

        /**
         *
         */
        foreach ($this->excludes as $exclude) {
            if (strpos($path, $exclude)) {
                return false;
            }
        }

        /**
         *
         */
        if (substr($path, -1) == '.') {
            return false;
        }

        return str_replace('\\', '/', $path);
    }

    /**
     * @param string $targetZipFilename
     * @param array  $paths
     * @param array  $tempContents
     * @param array  $packageInformation
     * @param string $sAppId
     * @param string $sAppDir
     *
     * @return array
     */
    public function export($targetZipFilename, $paths, $tempContents, $packageInformation = [], $sAppId = '', $sAppDir = '')
    {
        $checksumInformation = [];

        if (file_exists($targetZipFilename)) {
            if (!@unlink($targetZipFilename)) {
                exit(sprintf('Unable write to "%s"', $targetZipFilename));
            }
        }

        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (!is_dir($targeDir = dirname($targetZipFilename))) {
            mkdir($targeDir, 0777, true);
            chmod($targeDir, 0777);

        }

        $zipArchive = new \ZipArchive();
        $zipArchive->open($targetZipFilename, \ZipArchive::CREATE);

        $result = [];
        foreach ($paths as $path) {

            $path = realpath($path);
            if (is_file($path)) {
                $local = $this->normalizeFileName($path);
                $zipArchive->addFile($path, $local);
                $checksumInformation[] = $local;
                continue;
            }


            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), null);
            foreach ($iterator as $fileInfo) {

                $pathname = $fileInfo->getPathName();

                $local = $this->normalizeFileName($pathname);
                if (!$local) {
                    continue;
                }
                if (strpos($local, '.DS_Store') !== false) {
                    continue;
                }

                if ($fileInfo->isDir()) {
                    $zipArchive->addEmptyDir($local);
                } else {
                    if (!empty($sAppDir) && !empty($sAppId)) {
                        $zipArchive->addFile($pathname, str_replace($sAppDir, $sAppId, $local));
                    } else {
                        $zipArchive->addFile($pathname, $local);
                    }
                }
                if (!empty($sAppDir) && !empty($sAppId)) {
                    $checksumInformation[] = str_replace($sAppDir, $sAppId, $local);
                } else {
                    $checksumInformation[] = $local;
                }
            }
        }
        $tempContents['package.json'] = json_encode($packageInformation, JSON_PRETTY_PRINT);

        foreach ($tempContents as $local => $content) {
            $local = trim(str_replace('\\', '/', $local), '/');
            $zipArchive->addFromString($local, $content);
            if (!empty($sAppDir) && !empty($sAppId)) {
                $checksumInformation[] = str_replace($sAppDir, $sAppId, $local);
            } else {
                $checksumInformation[] = $local;
            }
        }
        $checksumInformation[] = 'checksum.json';
        $zipArchive->addFromString('checksum.json', json_encode($checksumInformation, JSON_PRETTY_PRINT));


        $zipArchive->close();

        return $result;
    }

    /**
     * Build checksum
     * @param $checksumDir
     * @param $buildDirs
     * @param array $aIgnorePaths
     */
    public function createChecksum($checksumDir, $buildDirs, $aIgnorePaths = [])
    {
        $content = '';
        foreach ($buildDirs as $buildDir) {
            $content .= $this->_generateChecksum($buildDir, $aIgnorePaths);
        }
        // update checksum file
        file_put_contents($checksumDir . 'checksum', $content);
    }

    private function _generateChecksum($sDir, $aIgnorePaths = [])
    {
        $content = '';
        if (is_dir($sDir)) {
            $iter = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            chdir($sDir);
            foreach ($iter as $path => $dir) {
                if ($dir instanceof SplFileInfo && $dir->isFile()) {

                    $file = $dir->getRealPath();
                    $hash = md5(file_get_contents($file));
                    $file = str_replace(PHPFOX_ROOT, '', $file);

                    if ($this->_isInvalidFile($file, $aIgnorePaths) || in_array($dir->getFilename(), ['.DS_Store', '.htaccess', 'checksum'])) {
                        continue;
                    }

                    $content .= $hash . " " . $file . "\n";
                }
            }
        } else {
            $hash = md5(file_get_contents($sDir));
            $file = str_replace(PHPFOX_ROOT, '', $sDir);

            if ($this->_isInvalidFile($file, $aIgnorePaths) || in_array(basename($file), ['.DS_Store', '.htaccess', 'checksum'])) {
                return '';
            }

            $content .= $hash . " " . $file . "\n";
        }

        return $content;
    }

    private function _isInvalidFile($file, $aIgnorePaths = [])
    {
        foreach ($aIgnorePaths as $ignorePath) {
            if (strpos($file, $ignorePath) !== false) {
                return true;
            }
        }

        return substr($file, -15) == 'server.sett.php'
            || substr($file, -14) == 'debug.sett.php'
            || strpos($file, '.git') !== false
            || strpos($file, '.idea') !== false
            || $file == 'PF.Base/module/admincp/include/service/setting/process.class.php';
    }
}
