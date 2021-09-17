<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Phpfox_Cache_Storage_Driver extends Phpfox_Cache_Abstract
{
	/**
	 * Array of all the cache files we have saved.
	 *
	 * @var array
	 */
	private $_aName = array();

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
	 * @var \phpFastCache\Core\DriverAbstract
	 */
	private $_driver;

    public function setup($params = [])
    {
        if (isset($params['driver']) && $params['driver'] instanceof \phpFastCache\Core\DriverAbstract) {
            $this->_driver = $params['driver'];
        }
	}

	public function __construct($params = [], \phpFastCache\Core\DriverAbstract $object = null) {
		parent::__construct($params);

		if (isset($params['driver']) && $params['driver'] instanceof \phpFastCache\Core\DriverAbstract) {
			$this->_driver = $params['driver'];
		}
	}

	/**
	 * Sets the name of the cache.
	 *
	 * @param string $sName Unique fill name of the cache.
	 * @param string $sGroup Optional param to identify what group the cache file belongs to
	 * @return string Returns the unique ID of the file name
	 */
	public function set($sName, $sGroup = '')
	{
		if (is_array($sName))
		{
			if (PHPFOX_SAFE_MODE || PHPFOX_OPEN_BASE_DIR)
			{
				$sName = str_replace(array('/', PHPFOX_DS), '_', $sName[0]) . '_' . $sName[1];
			}
			else
			{
				if ($sName[0] == 'feeds')
				{
					$sName[0] = $sName[0] . Phpfox_Locale::instance()->getLangId();
				}
				$sName = rtrim($sName[0], '/') . PHPFOX_DS . $sName[1];
			}
		}

		$sId = $sName;

		$this->_aName[$sId] = $sName;
		$this->_sName = $sName;

		if ($sGroup == 'memory')
		{
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
	 * @return object Returns the classes object.
	 */
	public function skipClose($bClose)
	{
		$this->_bSkipClose = $bClose;

		return $this;
	}

	/**
	 * We attempt to get the cache file. Also used within an IF conditional statement
	 * to check if the file has already been cached.
	 *
	 * @see self::set()
	 * @param string $sId Unique ID of the file we need to get. This is what is returned from when you use the set() method.
	 * @param int $iTime By default this is left blank, however you can identify how long a file should be cached before it needs to be updated in minutes.
	 * @return mixed If the file is cached we return the data. If the file is cached but empty we return a true (bool). if the file has not been cached we return false (bool).
	 */
	public function get($sId, $iTime = 0)
	{
		// We don't allow caching during an install or upgrade.
		if (defined('PHPFOX_INSTALLER'))
		{
			return false;
		}

		if (!$this->isCached($sId, $iTime)) {
			return false;
		}

		$aContent = json_decode($this->_driver->get($this->_getName($sId)), true);

		if(!is_array($aContent)){
		    return $aContent;
        }

		if (!is_array($aContent) && empty($aContent))
		{
			return true;
		}

		return $aContent;
	}

	/**
	 * Save data to the cache.
	 *
	 * @see self::set()
	 * @param string $sId Unique ID connecting to the cache file based by the method set()
	 * @param string|array $mContent Content you plan on saving to cache. Can be bools, strings, ints, objects, arrays etc...
     *
     * @return bool
	 */
	public function save($sId, $mContent)
	{
		if (defined('PHPFOX_INSTALLER'))
		{
			return false;
		}

		if (is_object($mContent)) {
			$mContent = (array) $mContent;
		}

		$this->_driver->set($this->_getName($sId), json_encode($mContent));

		return true;
	}

	/**
	 * Close the cache connection.
	 *
	 * @param string $sId ID of the cache file we plan on closing the connection with.
	 */
	public function close($sId)
	{
		unset($this->_aName[$sId]);
	}

	/**
	 * Removes cache file(s).
	 *
	 * @param string $sName Name of the cache file we need to remove.
	 * @param string $sType Pass an optional command to execute a specific routine.
	 * @return bool Returns true if we were able to remove the cache file and false if the system was locked.
	 */
	public function remove($sName = null, $sType = '')
	{        
		if ($sName === null) {
			$this->_driver->clean();
		} else {
			$this->_driver->delete($this->_getName($sName));
		}


		return true;
	}

	/**
	 * Checks if a file is cached or not.
	 *
	 * @param string $sId Unique ID of the cache file.
	 * @param string $iTime By default no timestamp check is done, however you can pass an int to check how many minutes a file can be cached before it must be re-cached.
	 * @return bool Returns true if the file is cached and false if the file hasn't been cached already.
	 */
	public function isCached($sId, $iTime = 0)
	{
		if (Phpfox::getParam('core.cache_skip'))
		{
			return false;
		}

		if ($this->_driver->isExisting($this->_getName($sId))) {
			return true;
		}

		return false;
	}

    public function getCachedFiles() {
        $s = $this->_driver->stats();
        $rows = [];
        if (isset($this->_driver->config) && $this->_driver->config['storage'] == 'memcached') {
            foreach ($s['data'] as $server => $values) {
                $cnt = $values['total_items'];
                $size = number_format($values['bytes'] / ( 1 << 20), 2);
                break;
            }
        } else {
            $cnt = $s['data']['Keyspace']['db0']['keys'];
            $size = $s['data']['Memory']['used_memory_human'];
        }
        
        $this->_aStats = array(
            'total' => $cnt,
            'size' => $size,
            'last' => null
        );

        return array($cnt, $rows);
    }

	/**
	 * Returns the full path to the cache file.
	 *
	 * @param string $sFile File name of the cache
	 * @return string Full path to the cache file.
	 */
	private function _getName($sFile)
	{
        if (is_array($sFile)) {
            if (PHPFOX_SAFE_MODE || PHPFOX_OPEN_BASE_DIR)
            {
                $sFile = str_replace(array('/', PHPFOX_DS), '_', $sFile[0]) . '_' . $sFile[1];
            }
            else
            {
                if ($sFile[0] == 'feeds')
                {
                    $sFile[0] = $sFile[0] . Phpfox_Locale::instance()->getLangId();
                }
                $sFile = rtrim($sFile[0], '/') . PHPFOX_DS . $sFile[1];
            }
        }
		return 'phpfox.'. $sFile;
	}
}