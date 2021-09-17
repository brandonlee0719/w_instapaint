<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

use phpFastCache\CacheManager;

Phpfox::getLibClass('phpfox.cache.abstract');

/**
 * Class is used to cache any sort of data that is passed via PHP. 
 * Currently there is support for file based caching and memcached.
 * It is perfect to store output from SQL queries that do not need to be executed
 * each time a user visits a specific page.
 * 
 * Example of using the cache system:
 * <code>
 * $oCache = Phpfox::getLib('cache');
 * // Create a name for your cache file
 * $sCacheId = $oCache->set('cache_file_name');
 * // Check if the file is already cached
 * if (!($aData = $oCache->get($sCacheId)))
 * {
 * 		// Run SQL query here...
 * 		$aData = array(1, 2, 3, 4);
 * 		// Store data in the the cache file (eg. strings, arrays, bool, objects etc...)
 * 		$oCache->save($sCacheId, $aData);
 * }
 * print_r($aData); 
 * </code>
 * 
 * If you want to remove a cache file:
 * <code>
 * Phpfox::getLib('cache')->remove('cache_file_name');
 * </code>
 * 
 * If you want to get all the files that have been cached:
 * <code>
 * // Array of files.
 * $aFiles = Phpfox::getLib('cache')->getCachedFiles();
 * </code>
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: cache.class.php 1666 2010-07-07 08:17:00Z Raymond_Benc $
 */
class Phpfox_Cache
{
	/**
	 * Object of the storage class.
	 *
	 * @var object
	 */
	private static $_oObject = null;

    private static $_driver = null;
	
	/**
	 * Based on what storage system is set within the global settings this is where we load the file.
	 * You can also pass any params to the storage object.
	 *
	 * @param array $aParams Any extra params you may want to pass to the storage object.
	 */
	public function __construct($aParams = array())
	{
		if (!self::$_oObject)
		{
			$cache = null;
			if (!self::$_oObject) {

				$driver = 'file';
				$cache_file = null;
				if (file_exists(PHPFOX_DIR_SETTINGS . 'cache.sett.php')) {
					$cache_file = require(PHPFOX_DIR_SETTINGS . 'cache.sett.php');

					$driver = $cache_file['driver'];
				}

				try {
					switch ($driver) {
						case 'redis':
							if (!isset($cache_file['redis'])) {
								throw new \Exception('Redis not set.');
							}

                            if (empty($cache_file['redis']['host']) || empty($cache_file['redis']['port'])) {
                                throw new \Exception('No host/port set.');
                            }

							CacheManager::setup($cache_file);

							$cache = CacheManager::getInstance('predis');
							break;
						case 'memcached':
                            if (!isset($cache_file['memcached'])) {
                                throw new \Exception('Memcache not set.');
                            }

                            if (!isset($cache_file['memcached'][0])) {
                                throw new \Exception('Missing server details for Memcache');
                            }

                            foreach ($cache_file['memcached'][0] as $value) {
                                if (empty($value)) {
                                    throw new \Exception('Memcache server value not set.');
                                }
                            }

							CacheManager::setup(['memcache' => $cache_file['memcached']]);

							$cache = CacheManager::getInstance('memcached');

							break;
					}
				} catch (\Exception $e) {
					$driver = 'file';
				}

				if ($driver != 'file') {
					if ($cache->fallback) {
						$driver = 'file';
						$cache = null;
					}
					else {
						$aParams['driver'] = $cache;
					}
				}

                self::$_driver = $driver;

				self::$_oObject = Phpfox::getLib('cache.storage.' . ($driver == 'file' ? 'file' : 'driver'));

				self::$_oObject->setup($aParams);
			}
		}
	}

    public function factory()
    {
        return self::$_oObject;
	}

	/**
	 * Return the object of the storage object.
	 *
	 * @return object Object provided by the storage class we loaded earlier.
	 */	
	public function &getInstance()
	{
		return self::$_oObject;
	}

    public static function driver() {
        return self::$_driver;
    }

	/**
	 * @return Phpfox_Cache_Storage_File
	 */
	public static function instance() {
		if (!self::$_oObject) {
			new self();
		}

		return self::$_oObject;
	}
}