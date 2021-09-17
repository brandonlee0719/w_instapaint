<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Image Helper
 * Displays all the images we see on a phpFox site. Each image runs thru this class where
 * we perform many sanity and file size checks before they are displayed on a site.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: helper.class.php 7287 2014-04-28 16:29:52Z Fern $
 */
class Phpfox_Image_Helper
{
	/**
	 * @return Phpfox_Image_Helper
	 */
	public static function instance() {
		return Phpfox::getLib('image.helper');
	}
	
	/**
	 * Returns a new width/height for an image based on the max arguments passed
	 *
	 * @param string $sImage Full path to the image
	 * @param int $iMaxHeight Max height of the image
	 * @param int $iMaxWidth Max width of the image
	 * @param int $iWidth Actual width of the image (optional)
	 * @param int $iHeight Actual height of the image (optional)
	 * @return array Returns an ARRAY, where argument 1 is the new height and argument 2 is the new width
	 */
	public function getNewSize($sImage = null, $iMaxHeight, $iMaxWidth, $iWidth = 0, $iHeight = 0)
	{
		if (is_array($sImage))
		{
			if(Phpfox::getParam('core.allow_cdn') && !Phpfox::getParam('core.keep_files_in_server') && isset($sImage[1]) && isset($sImage[2]))
			{
				$iWidth = $sImage[1];
				$iHeight = $sImage[2];
			}
			$sImage = $sImage[0];
		}
		else
		{
			if ($sImage !== null && (!file_exists($sImage) || filesize($sImage) < 1))
			{
				return array(0, 0);
			}
		}
			
	    if (!$iWidth && !$iHeight)
	    {
            if (file_exists($sImage)){
                list($iWidth, $iHeight) = getimagesize($sImage);
            } else {
                $iWidth = 0;
                $iHeight = 0;
            }
	    }
	    
	    $k = "";		
	    //get scaling factor
	    if ($iMaxWidth && $iMaxHeight && $iWidth && $iHeight)
	    {
	        $kX = $iMaxWidth / $iWidth;
	        $kY = $iMaxHeight / $iHeight;
	        $k = min($kX, $kY);
	    }
	    elseif ($iMaxHeight && $iHeight)
	    {
	        $k = $iMaxHeight / $iHeight;
	    }
	    elseif ($iMaxWidth && $iWidth)
	    {
	        $k = $iMaxWidth / $iWidth;
	    }
	
	    //correct scaling factor
	    if (((0 >= $k) || ($k > 1)))
	    {
	        $k = 1;
	    }
	
	    $iHeight *= $k;
	    $iWidth *= $k;	    

		if ($iHeight < 1)
		{
			$iHeight = 1;
		}
		if ($iWidth < 1)
		{
			$iWidth = 1;
		}
	    return array(round($iHeight), round($iWidth));
	}
	
	/**
	 * Displays an image on the site based on params passed
	 *
	 * @param array $aParams Holds an ARRAY of params about the image
	 * @return string Returns the HTML <image> or the full path to the image based on the params passed with the 1st argument
	 */
	public function display($aParams, $bIsLoop = false)
	{
		static $aImages = array();
		static $sPlugin;

		if(null === $sPlugin){
		    $sPlugin =  Phpfox_Plugin::get('image_helper_display_start');
        }
		
		// Create hash for cache
		$sHash = md5(serialize($aParams));
		
		// Return cached image
		if (isset($aImages[$sHash]))
		{
			return $aImages[$sHash];
		}
		
		$isObject = false;
				
		if ($sPlugin){eval($sPlugin);if (isset($mReturnPlugin)){return $mReturnPlugin;}}

		if (isset($aParams['theme']))
		{
			if (substr($aParams['theme'], 0, 5) == 'ajax/') {
				$type = str_replace(['ajax/', '.gif'], '', $aParams['theme']);
				$image = '';
				switch ($type) {
					case 'large':
						$image = '<i class="fa fa-spin fa-circle-o-notch _ajax_image_' . $type . '"></i>';
						break;
				}

				return $image;
			}

			$sSrc = Phpfox_Template::instance()->getStyle('image', $aParams['theme']);

			if(isset($aParams['return_url']) && $aParams['return_url']){
			    return $sSrc;
            }

			return '<img src="' . $sSrc . '">';
		}
				
		if (isset($aParams['max_height']) && !is_numeric($aParams['max_height']))
		{
			$aParams['max_height'] = Phpfox::getParam($aParams['max_height']);
		}
		
		if (isset($aParams['max_width']) && !is_numeric($aParams['max_width']))
		{
			$aParams['max_width'] = Phpfox::getParam($aParams['max_width']);
		}
		
		// Check if this is a users profile image
		$bIsOnline = false;
		$sSuffix = '';
        $bIsProfilePhoto = false;
		if (isset($aParams['user']))
		{
            $bIsProfilePhoto = true;
			if (isset($aParams['user_suffix']))
			{
				$sSuffix = $aParams['user_suffix'];	
			}
			// Create the local params
			$aParams['server_id'] = (isset($aParams['user']['user_' . $sSuffix . 'server_id']) ? $aParams['user']['user_' . $sSuffix . 'server_id'] : (isset($aParams['user'][$sSuffix . 'server_id']) ? $aParams['user'][$sSuffix . 'server_id'] : '')) ;
			$aParams['file'] = $aParams['user'][$sSuffix . 'user_image'];
			$aParams['path'] = 'core.url_user';
			if (isset($aParams['user']['' . $sSuffix . 'is_user_page'])) {
				$aParams['path'] = 'pages.url_image';
				$aParams['suffix'] = '_120';
			}
			$aParams['title'] = ($bIsOnline ?
                _p('full_name_is_online', array('full_name' => Phpfox::getLib('parse.output')->shorten($aParams['user'][$sSuffix . 'full_name'], 0)))
                : Phpfox::getLib('parse.output')->shorten($aParams['user'][$sSuffix . 'full_name'], 0));
			
			// Create the users link
			if(!empty($aParams['user']['profile_page_id']) && empty($aParams['user']['user_name']))
			{
                if (isset($aParams['user']['item_type']) && $aParams['user']['item_type'] == 1){
                    $sLink = Phpfox_Url::instance()->makeUrl('groups', $aParams['user']['profile_page_id']);
                } else {
                    $sLink = Phpfox_Url::instance()->makeUrl('pages', $aParams['user']['profile_page_id']);
                }
			}
			else
			{
				$sLink = Phpfox_Url::instance()->makeUrl('profile', $aParams['user'][$sSuffix . 'user_name']);
			}

			if (Phpfox::isUser()) {
                $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
                if (!empty($aBlockedUserIds) && in_array($aParams['user'][$sSuffix . 'user_id'], $aBlockedUserIds)) {
                    unset($sLink);
                }
            }

            if (isset($aParams['href']) && filter_var($aParams['href'], FILTER_VALIDATE_URL)){
                $sLink = $aParams['href'];
            }
			if (Phpfox::getParam('user.prevent_profile_photo_cache')
				&& isset($aParams['user'][$sSuffix . 'user_id'])
				&& $aParams['user'][$sSuffix . 'user_id'] == Phpfox::getUserId())
			{
				$aParams['time_stamp'] = true;
			}

			if (Phpfox::getCookie('recache_image')
				&& isset($aParams['user'][$sSuffix . 'user_id'])
				&& $aParams['user'][$sSuffix . 'user_id'] == Phpfox::getUserId())
			{
				$aParams['time_stamp'] = true;
			}

			if (substr($aParams['file'], 0, 1) == '{') {
				$isObject = true;
				$aParams['org_file'] = $aParams['file'];
			}
		}		

		if (empty($aParams['file'])) {

				if (isset($aParams['path'])
					&& ($aParams['path'] == 'core.url_user' || $aParams['path'] == 'pages.url_image')
				)
				{
					static $aGenders = null;
					
					if ($aGenders === null)
					{
						$aGenders = array();
						foreach ((array) Phpfox::getParam('user.global_genders') as $iKey => $aGender)
						{
							if (isset($aGender[3]))
							{
								$aGenders[$iKey] = $aGender[3];
							}
						}						
					}
					
					$sGender = '';				
					if (isset($aParams['user']) && isset($aParams['user'][$sSuffix . 'gender']))
					{					
						if (isset($aGenders[$aParams['user'][$sSuffix . 'gender']]))
						{
							$sGender = $aGenders[$aParams['user'][$sSuffix . 'gender']] . '_';	
						}
					}
					
					$sImageSuffix = '';
					if (!empty($aParams['suffix']))
					{
						$aParams['suffix'] = str_replace('_square', '', $aParams['suffix']); 				
						$iHeight = ltrim($aParams['suffix'], '_');
						$iWidth = ltrim($aParams['suffix'], '_');
						if ((int) $iWidth >= 200)
						{
						}
						else 
						{
							$sImageSuffix = $aParams['suffix'];				
						}					
					}		
					
					$sImageSize = $sImageSuffix;
					$name = (isset($aParams['user']) ? $aParams['user'][$sSuffix . 'full_name'] : (isset($aParams['title']) ? $aParams['title'] : ''));

                    $parts = explode(' ', $name);
                    $name = trim($name);
                    $first = 'P';
                    $last = 'F';
                    if (strlen($name) >= 2) {
                            $first = mb_substr($name, 0, 1);
                            $last = mb_substr($name, 1, 1);
                        if (isset($parts[1])) {
                            $lastChar = trim($parts[1]);
                            if (!empty($lastChar)) {
                                $last = mb_substr($lastChar, 0, 1);
                            }
                        }
                    } elseif (strlen($name) >= 1) {
                            $first = mb_substr($name, 0, 1);
                            $last = mb_substr($name, 0, 1);
            }
						if (isset($aParams['max_width'])) {
							$sImageSize = '_' . $aParams['max_width'];
						}

						$ele = 'a';
						if (isset($aParams['no_link']) || !isset($sLink) || (isset($aParams['user']) && isset($aParams['user'][$sSuffix . 'no_link']))) {
							$ele = 'span';
						}
                        if (ctype_alnum($first . $last)){
                            $namekey  = preg_replace('/[^a-z]/m','p',strtolower($first.$last));
                        } else {
                            $words = base64_encode($first . $last);
                            $words = strtolower(preg_replace("/[^a-z]+/", "", $words));
                            $namekey = mb_substr($words, 0, 2);
                            if (!ctype_alnum($namekey)){
                                $namekey = 'no_utf8';
                            }
                        }
                    if (strlen($namekey) == 1){
                        $namekey .= $namekey;
                    } elseif (strlen($namekey) == 0){
                        $namekey = 'pf';
                    }


                    if (isset($aParams['class']) && $aParams['class'] == 'js_hover_title')
                    {
                        $aParams['title'] = Phpfox::getLib('parse.output')->shorten($aParams['title'], 100, '...');
                    }

						$image = '<' . $ele . '' . ($ele == 'a' ? ' href="' . $sLink . '"' : '') . ' class="no_image_user ' . (isset($aParams['class']) ? $aParams['class'] : '') . ' _size_' . $sImageSize . ' _gender_' . $sGender . ' _first_' . $namekey . '"' . (((empty($aParams['class']) || $aParams['class'] != 'js_hover_title') && isset($aParams['title'])) ? ' title="' . $aParams['title'] . '" ' : '') . '>' . (isset($aParams['title']) ? '<span class="js_hover_info hidden">' . $aParams['title'] . '</span>' : '') . '<span>' . $first . $last . '</span></' . $ele . '>';


						return $image;
				}
				else 
				{
					$sImageSize = '';
					if (isset($aParams['suffix'])) {
						$sImageSize = $aParams['suffix'];
					}
					if (isset($aParams['max_width'])) {
						$sImageSize = $aParams['max_width'];
					}

					if (!empty($aParams['default_photo'])) {
						$file = flavor()->active->default_photo($aParams['default_photo'], true);
						$image = '<img class="default_photo i_size_' . $sImageSize . '" src="' . $file . '" />';
						return $image;
					}
					$ele = 'span';
					$image = '<' . $ele . ' class="no_image_item i_size_' . $sImageSize . '"><span></span></' . $ele . '>';

					return $image;
				}
			}

		if (isset($aParams['no_link']) && $aParams['no_link'])
		{
			unset($sLink);
		}

		$aParams['file'] = preg_replace('/%[^s]/', '%%', $aParams['file']);
        $sPathUrl = !empty($aParams['path']) ? Phpfox::getParam($aParams['path']) : '';
        $sSuffixNew = isset($aParams['suffix']) ? $aParams['suffix'] : '';
        if ($sSuffixNew == '_50') {
            $sSuffixNew = '_120';
        } elseif ($sSuffixNew == '_50_square') {
            $sSuffixNew = '_120_square';
        }
		$sSrc = $sPathUrl . sprintf($aParams['file'], $sSuffixNew);

        //Remove for performance issue
//		if (isset($aParams['suffix'])){
//            if (!$this->checkThumbnail($sSrc, $aParams['path'], $aParams['file'],$aParams['suffix'], isset($aParams['server_id']) ? $aParams['server_id']: 0)) {
//                //get original image if thumbnail doesn't exists.
//                $sSrc = $sPathUrl . sprintf($aParams['file'], '');
//            }
//        }

		$sDirSrc = str_replace(Phpfox::getParam('core.path_actual') . 'PF.Base/', PHPFOX_DIR, $sSrc);
		$sDirSrc = str_replace('/', PHPFOX_DS, $sDirSrc);
		if (isset($aParams['server_id']))
		{
			$newPath = Phpfox_Cdn::instance()->getUrl($sSrc, $aParams['server_id']);
			if (!empty($newPath)) {
				$sSrc = $newPath;
			}
		}

		if (!file_exists($sDirSrc)) {
			$sDirSrc = str_replace('PF.Base' . PHPFOX_DS, '', $sDirSrc);
			if (file_exists($sDirSrc)) {
				$sSrc = str_replace('PF.Base/', '', $sSrc);
			} else {
				$aParams['file'] = '';
			}
		}

		// Use thickbox effect?
		if (isset($aParams['thickbox']) && !(isset($aParams['no_link']) && $aParams['no_link']))
		{
			// Remove the image suffix (eg _thumb.jpg, _view.jpg, _75.jpg etc...).
			if (preg_match('/female\_noimage\.png/i', $sSrc))
			{
				$sLink = $sSrc;
			}
			elseif (preg_match('/^(.*)_(.*)_square\.(.*)$/i', $sSrc, $aMatches))
			{
				$sLink = $aMatches[1] . (isset($aParams['thickbox_suffix']) ? $aParams['thickbox_suffix'] : '') . '.' . $aMatches[3];
			}
			else
			{
				$sLink = preg_replace("/^(.*)_(.*)\.(.*)$/i", "$1" . (isset($aParams['thickbox_suffix']) ? $aParams['thickbox_suffix'] : '') . ".$3", $sSrc);
			}
		}

		// Windows slash fix
		$sSrc = str_replace("\\", '/', $sSrc);
		$sSrc = str_replace("\"", '\'', $sSrc);

		if (isset($aParams['return_url']) && $aParams['return_url'])
		{
			return $sSrc . (isset($aParams['time_stamp']) ? '?t=' . uniqid() : '');
		}

		if (isset($aParams['title']))
		{
			$aParams['title'] = Phpfox::getLib('parse.output')->clean(html_entity_decode($aParams['title'], null, 'UTF-8'));
		}

		$sImage = '';
		$sAlt = '';
		if (isset($aParams['alt_phrase']))
		{
			$sAlt = html_entity_decode(_p($aParams['alt_phrase']), null, 'UTF-8');
			unset($aParams['alt_phrase']);
		}

		if (isset($aParams['class']) && $aParams['class'] == 'js_hover_title')
		{
			$aParams['title'] = Phpfox::getLib('parse.output')->shorten($aParams['title'], 100, '...');
		}

		if (isset($sLink))
		{
			$sImage .= '<a href="' . $sLink;
			if (isset($aParams['thickbox']) && isset($aParams['time_stamp']))
			{
				$sImage .= '?t=' . uniqid();
			}
			$sImage .= '"';
			if (isset($aParams['title']))
			{
				$sImage .= ' title="' . htmlspecialchars($aParams['title']) . '"';
			}
			if (isset($aParams['thickbox']))
			{
				$sImage .= ' class="thickbox"';
			}
			if (isset($aParams['target']))
			{
				$sImage .= ' target="' . $aParams['target'] . '"';
			}
			$sImage .= '>';
		}

		$bDefer = true;
        if (defined('PHPFOX_AJAX_CALL_PROCESS') && PHPFOX_AJAX_CALL_PROCESS && !$isObject) {
            $bDefer = false;
        }

		$size = (isset($aParams['suffix']) ? $aParams['suffix'] : '');
		if (isset($aParams['max_width'])) {
			$size = $aParams['max_width'];
		}

		$aParams['class'] = ' _image_' . $size . ' ' . ($isObject ? 'image_object' : 'image_deferred') . ' ' . (isset($aParams['class']) ? ' ' . $aParams['class'] : '');

		$sImage .= ($bIsProfilePhoto) ? '<div class="img-wrapper"><img' : '<img';
		if ($bDefer == true)
		{
			if ($isObject) {
				$object = json_decode($aParams['org_file'], true);
				$sSrc = array_values($object)[0];
				$sImage .= ' data-object="' . array_keys($object)[0] . '" ';
			}
			if (!empty($aParams['no_lazy']))
				$sImage .= ' src="' . $sSrc . (isset($aParams['time_stamp']) ? '?t=' . uniqid() : '') . '" ';
			else {
				$sImage .= ' data-src="' . $sSrc . (isset($aParams['time_stamp']) ? '?t=' . uniqid() : '') . '" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" ';
			}
		}
		else
		{
			$sImage .= ' src="' . $sSrc . (isset($aParams['time_stamp']) ? '?t=' . uniqid() : '') . '" ';
		}

		if (isset($aParams['title']))
		{
			$sImage .= ' alt="' . htmlspecialchars($aParams['title']) . '" ';
		}
		else
		{
			$sImage .= ' alt="' . htmlspecialchars($sAlt) . '" ';
		}
		
		if (isset($aParams['js_hover_title']))
		{
			$sImage .= ' class="js_hover_title" ';
			unset($aParams['js_hover_title']);
		}
		
		if (isset($aParams['force_max']))
		{
			$iHeight = $aParams['max_height'];
			$iWidth = $aParams['max_width'];
		}
		
		if (!empty($iHeight))
		{
			$sImage .= 'height="' . $iHeight . '" ';
		}
		if (!empty($iWidth))
		{
			$sImage .= 'width="' . $iWidth . '" ';
		}
		
		unset($aParams['server_id'],
			$aParams['force_max'],
			$aParams['org_file'],
			$aParams['src'],
			$aParams['max_height'], 
			$aParams['max_width'], 
			$aParams['href'], 
			$aParams['user_name'], 
			$aParams['file'], 
			$aParams['suffix'], 
			$aParams['path'],
			$aParams['thickbox'],
			$aParams['no_default'],
			$aParams['full_name'],
			$aParams['user_id'],
			$aParams['time_stamp'],
			$aParams['user'],
			$aParams['title'],
			$aParams['theme'],
			$aParams['default'],
			$aParams['user_suffix'],
			$aParams['target'],
			$aParams['alt']
		);		
		
		foreach ($aParams as $sKey => $sValue)
		{
			$sImage .= ' '. $sKey . '="' . str_replace('"', '\"', $sValue) . '" ';
		}
        $sImage .= ($bIsProfilePhoto) ? '/></div>' : '/>';
		$sImage .= (isset($sLink) ? '</a>' : '');
		
		$aImages[$sHash] = $sImage;
		
		return $sImage;
	}

    public function checkRemoteFileExists($url)
    {
        $id = 'url' . md5($url);

        // caching result to reduce
        return get_from_cache($id, function () use ($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                // don't download content
                curl_setopt($ch, CURLOPT_NOBODY, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);

                return curl_exec($ch) !== false ? $url : 'null;';
            }) == $url;
    }

    /**
     * @param string $path
     * @param string $file
     * @param string $suffix
     * @param int $iServerId
     *
     * @return bool
     * @deprecated 4.6.0
     */
    private function autoCreateThumbnail($path, $file, $suffix, $iServerId = 0){
        if (empty($suffix)){
            return true;
        }
        //check file exist
        $urlPath = Phpfox::getParam($path);
        $urlSite = Phpfox::getParam('core.path_file');
        if (strpos($urlPath, $urlSite) === false){
            return false;
        }
        $dirPath = PHPFOX_DIR . str_replace($urlSite, '', $urlPath) . sprintf($file, $suffix);
        if (file_exists($dirPath)){
            return true;
        }

        $originalDirFile = PHPFOX_DIR . str_replace($urlSite, '', $urlPath) . sprintf($file, '');

        //Generate temp file if using cdn
        if (!file_exists($originalDirFile) && $iServerId != 0) {
            $sCdnUrl = $this->display(array(
                'server_id' => $iServerId,
                'path' => $path,
                'file' => $file,
                'suffix' => '',
                'return_url' => true
            ));

            if($this->checkRemoteFileExists($sCdnUrl)) {
                return true;
            }

            // Create a temp copy of the original file in local server
            file_put_contents($originalDirFile, fox_get_contents($sCdnUrl));
            //Delete file in local server
            register_shutdown_function(function () use ($originalDirFile) {
                @unlink($originalDirFile);
            });
        }

        $aAcceptSuffix = [
            '_10',
            '_10_square',

            '_15',
            '_15_square',

            '_20',
            '_20_square',

            '_30',
            '_30_square',

            '_40',
            '_40_square',

            '_50',
            '_50_square',

            '_60',
            '_60_square',

            '_70',
            '_70_square',

            '_75',
            '_75_square',

            '_80',
            '_80_square',

            '_90',
            '_90_square',

            '_100',
            '_100_square',

            '_110',
            '_110_square',

            '_120',
            '_120_square',

            '_150',
            '_150_square',

            '_175',
            '_175_square',

            '_200',
            '_200_square',

            '_250',
            '_250_square',

            '_300',
            '_300_square',

            '_350',
            '_350_square',

            '_400',
            '_400_square',

            '_450',
            '_450_square',

            '_500',
            '_500_square',

            '_600',
            '_600_square',
            '_333_square',

			'_1024',
        ];

        if (($sPlugin = Phpfox_Plugin::get('image_helper_auto_create_thumbnail'))){
            eval($sPlugin);
            if (isset($mReturnPlugin)){
                return $mReturnPlugin;
            }
        }

        if (!in_array($suffix, $aAcceptSuffix)){
            return false;
        }
        $oImage = Phpfox_Image::instance();
        $size = trim($suffix, '_');
        $size = trim($size, '_square');
        if (strpos($suffix, '_square') !== false){
            $oImage->createThumbnail($originalDirFile, $dirPath, $size, $size, false);
        } else {
            $oImage->createThumbnail($originalDirFile, $dirPath, $size, $size);
        }

        return true;
    }

    /**
     * @deprecated
     *
     * @param $sUrl
     * @param $path
     * @param $file
     * @param $suffix
     * @param int $iServerId
     * @return bool
     */
    private function checkThumbnail($sUrl, $path, $file, $suffix, $iServerId = 0){
        if (empty($suffix)){
            return true;
        }
        $urlPath = Phpfox::getParam($path);
        $urlSite = Phpfox::getParam('core.path_file');
        if (strpos($urlPath, $urlSite) === false){
            return true;
        }
        $dirPath = PHPFOX_DIR . str_replace($urlSite, '', $urlPath) . sprintf($file, $suffix);
        $originalDirFile = PHPFOX_DIR . str_replace($urlSite, '', $urlPath) . sprintf($file, '');

        //check file exists
        if ($iServerId != 0) {
            //check thumbnail
            $sCdnThumbUrl = Phpfox::getLib('cdn')->getUrl($sUrl,$iServerId);
            if ($this->checkRemoteFileExists($sCdnThumbUrl)) {
                return true;
            }
            //check original
            $sOriginalUrl = $urlPath . sprintf($file, '');
            $sCdnUrl = Phpfox::getLib('cdn')->getUrl($sOriginalUrl,$iServerId);
            if($this->checkRemoteFileExists($sCdnUrl)) {
                return false;
            }
        }
        elseif (file_exists($dirPath)) {
            return true;
        }
        elseif (file_exists($originalDirFile)) {
            return false;
        }

        return true;
    }
	/**
	 * Runs a check on two variables if they are equal, less then or greater then
	 *
	 * @param string $a Variable 1 to check against variable 2
	 * @param string $b Variable 2 to check against variable 1
	 * @return int Returns an INT based on the output
	 */
	private function _cmp($a, $b) 
	{
	    if ($a == $b) 
	    {
	        return 0;
	    }
	    return ($a < $b) ? -1 : 1;
	}	
}