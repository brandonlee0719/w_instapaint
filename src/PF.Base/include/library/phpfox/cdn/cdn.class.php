<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @method put($file, $name = null)
 * @method remove($file)
 * @method getServerId()
 * @method setServerId($server_id)
 */
class Phpfox_Cdn {
	private static $_object;

	private $oService;

	/**
	 * @return Phpfox_Cdn
	 */
	public static function instance() {
		return Phpfox::getLib('cdn');
	}

	public function &getInstance() {
		return $this;
	}

    public function factory()
    {
        return $this;
	}

	public function __call($method, $args) {
		$return = \Core\Event::trigger('lib_phpfox_cdn__call', $method, $args);
		if (is_string($return)) {
			return $return;
		}

		if (!self::$_object) {
			self::$_object = \Core\Event::trigger('lib_phpfox_cdn');
		}

		if (is_object(self::$_object)) {
			return call_user_func_array([self::$_object, $method], $args);
		}
        return null;
	}

	public function getUrl($sSrc, $iServerId = 0) {
		$method = 'getUrl';
		$args = [$sSrc, $iServerId];
		if ($iServerId == 0) {
			$oService = \Core\Event::trigger('lib_phpfox_cdn_service');
			if (is_object($oService)) {
				return call_user_func_array([$oService, $method], $args);
			}
			return $sSrc;
		}
		$return = \Core\Event::trigger('lib_phpfox_cdn__call', $method, $args);
		if (is_string($return)) {
			return $return;
		}

		$oService = $this->oService?: $this->oService = \Core\Event::trigger('lib_phpfox_cdn');
		
		if (is_object($oService)) {
			return call_user_func_array([$oService, $method], $args);
		}

		return null;
	}
}