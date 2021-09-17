<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Module_User
 */
class Module_User 
{	
	public static $aTables = array(
		'user',
		'user_activity',
		'user_blocked',
		'user_count',
		'user_css',
		'user_css_code',
		'user_custom',
		'user_custom_value',
		'user_dashboard',
		'user_delete',
		'user_delete_feedback',
		'user_design_order',
		'user_featured',
		'user_field',
		'user_gateway',
		'user_group',
		'user_group_custom',
		'user_group_setting',
		'user_inactive',
		'user_ip',
		'user_notification',
		'user_privacy',
		'user_promotion',
		'user_rating',
		'user_setting',
		'user_snoop',
		'user_space',
		'user_verify',
		'user_verify_error',
		'user_status',
		'upload_track',
		'user_custom_multiple_value',
		'user_custom_data',
		'point_purchase',
		'user_spam'
	);
	
	public static $aInstallWritable = array(
		'file/pic/user/',
		'file/pic/user/spam_question/'
	);		
}
