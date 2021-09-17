<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: common.sett.php 7303 2014-05-07 17:43:19Z Fern $
 */
defined('PHPFOX') or exit('NO DICE!');

$_CONF['core.http'] = 'https://';
$_CONF['core.https'] = 'http://';

$bIsHTTPS = false;

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
    $bIsHTTPS= true;
}elseif(isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == 443){
    $bIsHTTPS = true;
}elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $bIsHTTPS = true;
}elseif(isset($_SERVER['HTTP_CF_VISITOR']) and strpos($_SERVER['HTTP_CF_VISITOR'],'https')){
    $bIsHTTPS = true;
}

defined('PHPFOX_IS_HTTPS') or define('PHPFOX_IS_HTTPS', $bIsHTTPS);

$_CONF['core.folder_original'] = str_replace('/index.php/', '/', $_CONF['core.folder']);
$_CONF['core.path'] = (PHPFOX_IS_HTTPS ? 'https' : 'http') . '://' . $_CONF['core.host'] . $_CONF['core.folder'];
$_CONF['core.path_file'] = (PHPFOX_IS_HTTPS ? 'https' : 'http') . '://' . $_CONF['core.host'] . $_CONF['core.folder_original'] . 'PF.Base/';
$_CONF['core.path_actual'] = (PHPFOX_IS_HTTPS ? 'https' : 'http') . '://' . $_CONF['core.host'] . $_CONF['core.folder_original'];

$_CONF['core.dir_file'] = PHPFOX_DIR . 'file' . PHPFOX_DS;
$_CONF['core.url_file'] = $_CONF['core.path_file'] . 'file/';

$_CONF['core.dir_file_temp'] = PHPFOX_DIR . 'file' . PHPFOX_DS . 'temp' . PHPFOX_DS;
$_CONF['core.url_file_temp'] = $_CONF['core.path_file'] . 'file/temp/';


$_CONF['core.dir_cache'] = $_CONF['core.dir_file'] . 'cache' . PHPFOX_DS;
$_CONF['core.url_module'] = $_CONF['core.path_file'] . 'module/';

/* /file/ directories */
$_CONF['core.dir_pic'] = $_CONF['core.dir_file'] . 'pic' . PHPFOX_DS;
$_CONF['core.url_pic'] = $_CONF['core.url_file'] . 'pic/';

$_CONF['core.dir_attachment'] = $_CONF['core.dir_file'] . 'attachment' . PHPFOX_DS;
$_CONF['core.url_attachment'] = $_CONF['core.url_file'] . 'attachment/';

$_CONF['core.dir_user'] = $_CONF['core.dir_pic'] . 'user' . PHPFOX_DS;
$_CONF['core.url_user'] = $_CONF['core.url_pic'] . 'user/';

$_CONF['photo.dir_photo'] = $_CONF['core.dir_pic'] . 'photo' . PHPFOX_DS;
$_CONF['photo.url_photo'] = $_CONF['core.url_pic'] . 'photo/';

$_CONF['poll.dir_image'] = $_CONF['core.dir_pic'] . 'poll' . PHPFOX_DS;
$_CONF['poll.url_image'] = $_CONF['core.url_pic'] . 'poll/';

$_CONF['quiz.dir_image'] = $_CONF['core.dir_pic'] . 'quiz' . PHPFOX_DS;
$_CONF['quiz.url_image'] = $_CONF['core.url_pic'] . 'quiz/';

$_CONF['egift.dir_egift'] = $_CONF['core.dir_pic'] . 'egift' . PHPFOX_DS;
$_CONF['egift.url_egift'] = $_CONF['core.url_pic'] . 'egift/';

$_CONF['marketplace.dir_image'] = $_CONF['core.dir_pic'] . 'marketplace' . PHPFOX_DS;
$_CONF['marketplace.url_image'] = $_CONF['core.url_pic'] . 'marketplace/';

$_CONF['event.dir_image'] = $_CONF['core.dir_pic'] . 'event' . PHPFOX_DS;
$_CONF['event.url_image'] = $_CONF['core.url_pic'] . 'event/';

$_CONF['pages.dir_image'] = $_CONF['core.dir_pic'] . 'pages' . PHPFOX_DS;
$_CONF['pages.url_image'] = $_CONF['core.url_pic'] . 'pages/';

$_CONF['share.dir_image'] = $_CONF['core.dir_pic'] . 'bookmark' . PHPFOX_DS;
$_CONF['share.url_image'] = $_CONF['core.url_pic'] . 'bookmark/';

$_CONF['music.dir'] = $_CONF['core.dir_file'] . 'music' . PHPFOX_DS;
$_CONF['music.url'] = $_CONF['core.url_file'] . 'music/';

$_CONF['music.dir_image'] = $_CONF['core.dir_pic'] . 'music' . PHPFOX_DS;
$_CONF['music.url_image'] = $_CONF['core.url_pic'] . 'music/';

$_CONF['ad.dir_image'] = $_CONF['core.dir_pic'] . 'ad' . PHPFOX_DS;
$_CONF['ad.url_image'] = $_CONF['core.url_pic'] . 'ad/';

$_CONF['subscribe.dir_image'] = $_CONF['core.dir_pic'] . 'subscribe' . PHPFOX_DS;
$_CONF['subscribe.url_image'] = $_CONF['core.url_pic'] . 'subscribe/';

$_CONF['css.dir_cache'] = $_CONF['core.dir_file'] . 'css' . PHPFOX_DS;
$_CONF['css.url_cache'] = $_CONF['core.url_file'] . 'css/';

$_CONF['core.dir_watermark'] = $_CONF['core.dir_pic'] . 'watermark' . PHPFOX_DS;
$_CONF['core.url_watermark'] = $_CONF['core.url_pic'] . 'watermark/';

$_CONF['core.dir_icon'] = $_CONF['core.dir_pic'] . 'icon' . PHPFOX_DS;
$_CONF['core.url_icon'] = $_CONF['core.url_pic'] . 'icon/';

$_CONF['user.dir_user_spam'] = $_CONF['core.dir_pic'] . 'user' . PHPFOX_DS . 'spam_question' . PHPFOX_DS;
$_CONF['user.url_user_spam'] = $_CONF['core.url_pic'] . 'user/spam_question/';

/* Static URLS */
$_CONF['core.dir_static'] = PHPFOX_DIR . 'static' . PHPFOX_DS;
$_CONF['core.url_static'] = $_CONF['core.path_file'] . 'static/';
$_CONF['core.url_static_script'] = $_CONF['core.url_static'] . 'jscript/';
$_CONF['core.url_static_css'] = $_CONF['core.url_static'] . 'style/';
$_CONF['core.url_static_image'] = $_CONF['core.url_static'] . 'image/';
$_CONF['core.url_misc'] = $_CONF['core.url_static_image'] . 'misc/';

// Name of the thumbnail directory
$_CONF['core.url_thumb'] = 'thumb/';

// Default configurations for caching block data

$_CONF['core.cache_time'] = [
    0 => 0,
    1 => 1,
    5 => 5,
    10 => 10,
    30 => 30
];
$_CONF['core.cache_time_default'] = 5;
$_CONF['core.cache_time_admincp_default'] = 10;
$_CONF['core.cache_total'] = 100;
$_CONF['core.cache_rate'] = 1.2;

//location for submenu
$_CONF['core.sub_menu_location'] = '1';
$_CONF['core.items_per_page'] = '20';

//Here define settings do not allow Admin edit. If these settings set wrong value, site may break. Only developer can edit here, but check careful before edit.

//Techie Setting

require_once('techie.sett.php');