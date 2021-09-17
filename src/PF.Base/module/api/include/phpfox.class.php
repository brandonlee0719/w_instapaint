<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Module_Api
 */
class Module_Api
{
	public static $aDevelopers = array(
		array(
			'name' => 'Raymond Benc',
			'website' => 'www.phpfox.com'
		)
	);
	
	public static $aTables = array(
		'api_gateway',
		'api_gateway_log'
	);
}
