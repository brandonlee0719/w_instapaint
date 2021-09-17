<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Session Handler Loader
 * Loads session handlers: include/library/phpfox/session/handler/
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: handler.class.php 5557 2013-03-26 10:48:01Z Raymond_Benc $
 */
class Phpfox_Session_Handler
{
	/**
	 * Session object.
	 *
	 * @var object
	 */
	private static $_oObject = null;

	/**
	 * Class constructor which loads the session handler we should use.
	 *
	 * @return object
	 */
	public function __construct()
	{
		if (!self::$_oObject)
		{
			$sStorage = 'phpfox.session.handler.default';
			self::$_oObject = Phpfox::getLib($sStorage);
		}
	}
	
	/**
	 * Get session object.
	 *
	 * @return Phpfox_Session_Handler_Default
	 */
	public function &getInstance()
	{
		return self::$_oObject;
	}

	/**
	 * @return Phpfox_Session_Handler_Default
	 */
	public static function instance() {
		if (!self::$_oObject) {
			new self();
		}

		return self::$_oObject;
	}

	public function __call($method, $args) {
		return call_user_func_array([self::$_oObject, $method], $args);
	}
}