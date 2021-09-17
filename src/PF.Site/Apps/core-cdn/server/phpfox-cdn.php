<?php

set_time_limit(0);

if (!file_exists('./phpfox-cdn-setting.php'))
{
	exit('Missing config file.');
}

require_once('./phpfox-cdn-setting.php');

final class PHPFOX_CDN
{
	static $_bPass = true;
	static $_aMsg = array();
	static $_sDebug = '';
	
	public static function error($iErrorCode, $sMsg)
	{
		self::$_bPass = false;		
		self::$_aMsg['error_code'] = $iErrorCode;
		self::$_aMsg['error'] = $sMsg;

		echo json_encode(array('pass' => self::$_bPass, 'output' => self::$_aMsg));
		exit;
	}
	
	public static function debug($sDebug)
	{
		self::$_sDebug = $sDebug;
	}
	
	public static function isPassed()
	{
		return self::$_bPass;
	}
	
	public static function output()
	{
		echo json_encode(array('pass' => self::$_bPass, 'output' => self::$_aMsg));
	}
}

defined('CDN_FOLDER') || define('CDN_FOLDER', './file/');
defined('CDN_KEY') || define('CDN_KEY', '');

if (empty($_POST['action']))
{
	PHPFOX_CDN::error('MISSING_ACTION', 'Missing action.');
}
elseif (empty($_POST['cdn_key']))
{
	PHPFOX_CDN::error('MISSING_KEY', 'Missing CDN key.');
}
elseif ($_POST['cdn_key'] != CDN_KEY)
{
	PHPFOX_CDN::error('KEY_NOT_MATCH', 'Key does not match.');	
}
elseif (is_writable(CDN_FOLDER) != true)
{
    PHPFOX_CDN::error('NOT_WRITEABLE', 'The target folder is not writeable.');	
}
else
{
	switch ($_POST['action'])
	{
		case 'upload':
			if (empty($_FILES['upload']))
			{
				PHPFOX_CDN::error('NOTHING_WAS_UPLOADED', 'Nothing was uploaded.');
			}
			elseif (empty($_POST['file_name']))
			{
				PHPFOX_CDN::error('MISSING_FILE_NAME', 'Missing the filename of the file uploaded.');
			}
			else 
			{
				$sName = $_POST['file_name'];
				$sName = str_replace("\\", '/', $sName);
				$aParts = explode('.', $sName);

				$sub = explode('/', $sName);
				$file_name = $sub[count($sub) - 1];
				unset($sub[count($sub) - 1]);
				$path = CDN_FOLDER . '/' . implode('/', $sub) . '/';
				if (!is_dir($path)) {
					mkdir($path, 0777, true);
				}

				move_uploaded_file($_FILES['upload']['tmp_name'], $path . $file_name);
			}		
			break;
		
		case 'remove':
			if (empty($_POST['file_name']))
			{
				PHPFOX_CDN::error('MISSING_FILE_NAME', 'Missing the filename of the file to be removed.');
			}
			else 
			{
				$sName = $_POST['file_name'];
				$sName = str_replace("\\", '/', $sName);

				$sub = explode('/', $sName);
				$file_name = $sub[count($sub) - 1];
				unset($sub[count($sub) - 1]);
				$path = CDN_FOLDER . '/' . implode('/', $sub) . '/';
				
				unlink($path . $file_name);
			}
			break;
	}
}

PHPFOX_CDN::output();