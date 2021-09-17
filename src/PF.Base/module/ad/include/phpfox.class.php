<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Module_Ad
 */
class Module_Ad
{	
	public static $aTables = array(
		'ad',
		'ad_invoice',
		'ad_plan',
		'ad_sponsor',
		'ad_country'
	);
	
	public static $aInstallWritable = array(
		'file/pic/ad/'
	);		
}