<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * File Cache Layer - This class handles the cache routine for phpFox
 * and can store arrays, strings and objects. All the data is stored in a
 * flat file which has a unique id. Data is also serilazed before its stored
 * in the flat file.
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: file.class.php 6363 2013-07-25 09:14:30Z
 *                    Raymond_Benc $
 */
class Phpfox_Cache_Storage_File extends Phpfox_Cache_Abstract
{
    /**
     * Array of all the cache files we have saved.
     *
     * @var array
     */
    private $_aName = [];

    /**
     * Name of the current cache file we are saving
     *
     * @var string
     */
    private $_sName;

    /**
     * Set this to true and we will force the system to get the information from
     * a memory based caching system (eg. memcached)
     *
     * @var bool
     */
    private $_bFromMemory = false;

    /**
     * By default we always close a cache call automatically, however at times
     * you may need to close it at a later time and setting this to true will
     * skip closing the closing of the cache reference.
     *
     * @var bool
     */
    private $_bSkipClose = false;

    /**
     * @var bool
     */
    private $_bCacheSkip = false;

    /**
     * @var bool
     */
    private $_bAddSalt = false;

    /**
     * @var string
     */
    private $_sCacheSuffix = '';

    /**
     * @var string
     */
    private $_sSalt = '';

    /**
     * @var string
     */
    private $directory = PHPFOX_DIR_CACHE;

    /**
     * @var int
     */
    private $directoryPermission = 0777;

    /**
     * @var int
     */
    private $filePermission = 0777;

    /**
     * Phpfox_Cache_Storage_File constructor.
     *
     * @param array $aParams
     */
    public function __construct($aParams = [])
    {
        parent::__construct($aParams);

        $this->_bCacheSkip = !!Phpfox::getParam('core.cache_skip');
        $this->_sCacheSuffix = Phpfox::getParam('core.cache_suffix');
        $this->_bAddSalt = !!Phpfox::getParam('core.cache_add_salt');
        $this->_sSalt = Phpfox::getParam('core.salt');

    }

    /**
     * Sets the name of the cache.
     *
     * @param string $sName  Unique fill name of the cache.
     * @param string $sGroup Optional param to identify what group the cache
     *                       file belongs to
     *
     * @return string Returns the unique ID of the file name
     */
    public function set($sName, $sGroup = '')
    {
        if (is_array($sName)) {
            if (PHPFOX_SAFE_MODE || PHPFOX_OPEN_BASE_DIR) {
                $sName = str_replace(['/', PHPFOX_DS], '_', $sName[0]) . '_'
                    . $sName[1];
            } else {
                if ($sName[0] == 'feeds') {
                    $sName[0] = $sName[0] . Phpfox_Locale::instance()
                            ->getLangId();
                }
                $sName = rtrim($sName[0], '/') . PHPFOX_DS . $sName[1];
            }
        }

        $sId = $sName;

        $this->_aName[$sId] = $sName;
        $this->_sName = $sName;

        if ($sGroup == 'memory') {
            $this->_bFromMemory = true;
        }

        return $sId;
    }

    /**
     * By default we always close a cache call automatically, however at times
     * you may need to close it at a later time and setting this to true will
     * skip closing the closing of the cache reference.
     *
     * @param bool $bClose True to skip the closing of the connection
     *
     * @return object Returns the classes object.
     */
    public function skipClose($bClose)
    {
        $this->_bSkipClose = $bClose;

        return $this;
    }

    /**
     * We attempt to get the cache file. Also used within an IF conditional
     * statement to check if the file has already been cached.
     *
     * @see self::set()
     *
     * @param string $sId   Unique ID of the file we need to get. This is what
     *                      is returned from when you use the set() method.
     * @param int    $iTime By default this is left blank, however you can
     *                      identify how long a file should be cached before it
     *                      needs to be updated in minutes.
     *
     * @return mixed If the file is cached we return the data. If the file is
     *               cached but empty we return a true (bool). if the file has
     *               not been cached we return false (bool).
     */
    public function get($sId, $iTime = 0)
    {
        // We don't allow caching during an install or upgrade.
        if (defined('PHPFOX_INSTALLER')
            || defined('PHPFOX_DEVELOPING_NO_CACHE')
        ) {
            return false;
        }

        if (!$this->isCached($sId, $iTime)) {
            return false;
        }

        $aContent = require($this->_getName($this->_aName[$sId]));

        return $aContent;
    }

    /**
     * Save data to the cache.
     *
     * @see self::set()
     *
     * @param string $sId      Unique ID connecting to the cache file based by
     *                         the method set()
     * @param mixed $mContent Content you plan on saving to cache. Can be
     *                         bools, strings, ints, objects, arrays etc...
     *
     * @return bool
     */
    public function save($sId, $mContent)
    {
        if (defined('PHPFOX_INSTALLER')) {
            return false;
        }

        if (is_object($mContent)) {
            $mContent = (array)$mContent;
        }

        $sContent = '<?php return ' . var_export($mContent, true) . ';';

        $file = $this->_getName($this->_aName[$sId]);

        $tmp  = sys_get_temp_dir() . DIRECTORY_SEPARATOR .  'phpfox_'. uniqid('');

        file_put_contents($tmp, $sContent, LOCK_EX);
        
        if(file_exists($tmp)){
            chmod($tmp, $this->filePermission);
            @rename($tmp, $file);
        }
        return true;
    }

    /**
     * Close the cache connection.
     *
     * @param string $sId ID of the cache file we plan on closing the
     *                    connection with.
     */
    public function close($sId)
    {
        unset($this->_aName[$sId]);
    }

    /**
     * Removes cache file(s).
     *
     * @param string $sName Name of the cache file we need to remove.
     * @param string $sType Pass an optional command to execute a specific
     *                      routine.
     *
     * @return bool Returns true if we were able to remove the cache file and
     *              false if the system was locked.
     *
     * Param $sType is deprecated
     */
    public function remove($sName = null, $sType = '')
    {
        if (file_exists(PHPFOX_DIR_CACHE . 'cache.lock')) {
            return false;
        }

        if ($sName === null) {
            $this->flush();

            return true;
        }

        switch ($sType) {
            // deprecated, will be removed in 4.7.0
            case 'substr':

                $sDir = PHPFOX_DIR_CACHE . (is_array($sName) ? rtrim($sName[0],
                            '/') . PHPFOX_DS
                        : str_replace('_', PHPFOX_DS, $sName));
                if (!PHPFOX_SAFE_MODE && !PHPFOX_OPEN_BASE_DIR
                    && !is_dir($sDir)
                ) {
                    Phpfox_File::instance()
                        ->mkdir($sDir, true, $this->directoryPermission);
                }
                $aFiles = Phpfox_File::instance()->getFiles($sDir);
                foreach ($aFiles as $sFile) {
                    if (is_dir($sDir . PHPFOX_DS . $sFile)) {
                        //Do not other process write cache now
                        $this->lock();
                        Phpfox_File::instance()->delete_directory($sDir
                            . PHPFOX_DS . $sFile);
                        //Remove lock
                        $this->unlock();
                    } else {
                        unlink($sDir . PHPFOX_DS . $sFile);
                    }
                }

                if (is_array($sName)) {
                    $sName[0] = rtrim($sName[0], '/');
                    if ($sName[0] == 'feeds') {
                        $sName[0] = $sName[0] . Phpfox_Locale::instance()
                                ->getLangId();
                    }
                    $sName = $sName[0] . PHPFOX_DS . $sName[1];
                }
                $sName = $this->_getName($sName);
                if (file_exists($sName)) {
                    @unlink($sName);
                }
                break;
            case 'path':
                if (file_exists($sName)) {
                    @unlink($sName);
                }
                break;
            default:
                if (is_array($sName)) {
                    $sName[0] = rtrim($sName[0], '/');
                    if ($sName[0] == 'feeds') {
                        $sName[0] = $sName[0] . Phpfox_Locale::instance()
                                ->getLangId();
                    }
                    $sName = $sName[0] . PHPFOX_DS . $sName[1];
                }
                $sName = $this->_getName($sName);
                if (file_exists($sName)) {
                    @unlink($sName);
                }
        }

        return true;
    }

    /**
     * Checks if a file is cached or not.
     *
     * @param string $sId   Unique ID of the cache file.
     * @param string $iTime By default no timestamp check is done, however you
     *                      can pass an int to check how many minutes a file
     *                      can be cached before it must be re-cached.
     *
     * @return bool Returns true if the file is cached and false if the file
     *              hasn't been cached already.
     */
    public function isCached($sId, $iTime = 0)
    {
        if ($this->_bCacheSkip) {
            return false;
        }

        if (isset($this->_aName[$sId]) && file_exists($this->_getName($this->_aName[$sId]))) {
            if ($iTime && (PHPFOX_TIME - $iTime * 60) > (filemtime($this->_getName($this->_aName[$sId])))) {
                $this->remove($this->_aName[$sId]);
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Gets all the cache files and returns information about the cache file to
     * stats.
     *
     * @param array  $aConds SQL conditions (not used anymore)
     * @param string $sSort  Sorting the cache files
     * @param int    $iPage  Current page we are on
     * @param string $sLimit Limit of how many files to display
     *
     * @return array First array is how many cache files there are and the 2nd
     *               array holds all the cache files.
     */
    public function getCachedFiles($aConds = [], $sSort, $iPage, $sLimit)
    {
        $iCnt = 0;
        $aRows = [];
        $iSize = 0;
        $aFiles = Phpfox_File::instance()->getAllFiles(PHPFOX_DIR_CACHE, true);
        $iLastFile = 0;
        if (is_array($aFiles)) {
            foreach ($aFiles as $sFile) {
                $iSize += filesize($sFile);
                $iCnt++;

                if (filemtime($sFile) > $iLastFile) {
                    $iLastFile = filemtime($sFile);
                }
            }
        }

        $this->_aStats = [
            'total' => $iCnt,
            'size'  => $iSize,
            'last'  => $iLastFile,
        ];

        return [$iCnt, $aRows];
    }

    /**
     * Returns the full path to the cache file.
     *
     * @param string $sFile File name of the cache
     *
     * @return string Full path to the cache file.
     */
    private function _getName($sFile)
    {
        if ($this->_getParam('free')) {
            return $sFile;
        } elseif ($this->_getParam('path')) {
            return $sFile;
        }

        $sPath = PHPFOX_DIR_CACHE . ($this->_bAddSalt ? md5($this->_sSalt . $sFile) : $sFile) . $this->_sCacheSuffix;

        $aParts = pathinfo($sPath);
        $aSub = explode('_', $aParts['filename']);

        if (count($aSub) >= 1) {
            $sActualName = $aSub[count($aSub) - 1];
            unset($aSub[count($aSub) - 1]);
            $sDir = $aParts['dirname'] . PHPFOX_DS . implode(PHPFOX_DS, $aSub);
        } else {
            $sActualName = $sFile;
            $sDir = $aParts['dirname'] . PHPFOX_DS;
        }
        if (!is_dir($sDir) && !file_exists($sDir)) {
            Phpfox::getLib('file')->mkdir($sDir, true, $this->directoryPermission);
        }
        $sNewPath = rtrim($sDir, PHPFOX_DS) . PHPFOX_DS . $sActualName . '.php';
        $sNewPath = str_replace('/', PHPFOX_DS, $sNewPath);

        return $sNewPath;
    }

    public function flush()
    {
        Phpfox::getLib('file')->delete_directory($this->directory);
    }
}