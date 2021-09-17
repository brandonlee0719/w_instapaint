<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Interface Phpfox_Cache_Interface
 */
interface Phpfox_Cache_Interface
{
	/**
	 * Sets the name of the cache.
	 *
	 * @param string|array $sName Unique fill name of the cache.
	 * @param string $sGroup Optional param to identify what group the cache file belongs to
	 * @return string Returns the unique ID of the file name
	 */
	public function set($sName, $sGroup = '');

	/**
	 * Get cache by key
	 *
	 * @param string $sName Unique fill name of the cache.
	 * @return array|bool Returns cached value or false
	 */
	public function get($sName);

	/**
	 * Save value to cache
	 *
	 * @param string $sName cache ID
	 * @param array $aValue value to save cache
	 * @return bool
	 */
	public function save($sName, $aValue);

    /**
     * Remove cache by ID
     *
     * @param string $sName cache ID
     * @return bool
     */
	public function remove($sName = '');
}
