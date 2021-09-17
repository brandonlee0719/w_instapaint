<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Cleans text added to the database and already parsed/cleaned via Parse_Input.
 * This is the last attempt to clean out any invalid tags, usually fix invalid HTML tags. 
 * Anything that needs to be removed should have already been removed with Parse_Input to save 
 * on running such a heavy routine for each item. We also parse naughty words here as
 * this information needs to be checked each time in case new words are added by an Admin.
 * 
 * @see Parse_Input
 * @see Parse_Bbcode
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: output.class.php 7308 2014-05-08 14:55:48Z Fern $
 */
class Phpfox_Parse_Output
{
	/**
	 * Regex used to convert URL and emails.
	 * 
	 * @deprecated 2.0.0rc1
	 * @var array
	 */
	private $_aRegEx = array(
		'url_to_link' => '~(?>[a-z+]{2,}://|www\.)(?:[a-z0-9]+(?:\.[a-z0-9]+)?@)?(?:(?:[a-z](?:[a-z0-9]|(?<!-)-)*[a-z0-9])(?:\.[a-z](?:[a-z0-9]|(?<!-)-)*[a-z0-9])+|(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(?:/[^\\/:?*"<>|\n]*[a-z0-9])*/?(?:\?[a-z0-9_.%]+(?:=[a-z0-9_.%:/+-]*)?(?:&[a-z0-9_.%]+(?:=[a-z0-9_.%:/+-]*)?)*)?(?:#[a-z0-9_%.]+)?~is',
		'email' => '/[-a-zA-Z0-9._]+@[-a-zA-Z0-9._]+(\.[-a-zA-Z0-9._]+)/is'
	);

	private $_regex = [
		'hash_tags' => '/[^\w](#[\wa-zA-Z0-9\[\]\/]+)/u',
		'mentions' => '^\[user=(.*?)\]^'
	];
	
	/**
	 * Parsing settings for images.
	 *
	 * @var array
	 */
	private $_aImageParams = array();
	
	/**
	 * Parsing settings for video embeds.
	 *
	 * @var array
	 */
	private $_aEmbedParams = array();
	
	/**
	 * Defines if the string has been shortened or not.
	 *
	 * @var bool
	 */
	private $_bIsShortened = false;
	
	/**
	 * Defines if the string has reached the maximum break lines or not
	 *
	 * @var bool
	 */
	private $_bIsMaxLine = false;
	
	/**
	 * Class Constructor.
	 *
	 */
	public function __construct()
	{		
	}

	/**
	 * @return $this;
	 */
	public static function instance() {
		return Phpfox::getLib('parse.output');
	}
	
	/**
	 * Text we need to parse, usually text added via a <textarea>.
	 *
	 * @param string $sTxt is the string we need to parse
	 * @return string Parsed string
	 */
	public function parse($sTxt, $bParseNewLine = true)
	{
		if (empty($sTxt))
		{
			return $sTxt;
		}

		$sTxt = ' ' . $sTxt;

		(($sPlugin = Phpfox_Plugin::get('parse_output_parse')) ? eval($sPlugin) : null);

		if (isset($override) && is_callable($override)) {
			$sTxt = call_user_func($override, $sTxt);
		}
		elseif (!Phpfox::getParam('core.allow_html')) {
			$sTxt = $this->htmlspecialchars($sTxt);
		}
		else {
            $sTxt = $this->cleanScriptTag($sTxt);
        }

		$sTxt = Phpfox::getService('ban.word')->clean($sTxt);

		$sTxt = $this->parseUrls($sTxt);

		$sTxt = preg_replace_callback('/\[PHPFOX_PHRASE\](.*?)\[\/PHPFOX_PHRASE\]/i', array($this, '_getPhrase'), $sTxt);

		$sTxt = ' ' . $sTxt;
		$sTxt = Phpfox::getLib('parse.bbcode')->parse($sTxt);

		if (Phpfox::getParam('tag.enable_hashtag_support'))
		{
			$sTxt = $this->replaceHashTags($sTxt);
		}

		$sTxt = str_replace("\n\r\n\r", "", $sTxt);
		$sTxt = str_replace("\n\r", "", $sTxt);
        $sTxt = preg_replace('/>([\s]+)</m', "><", $sTxt);

        //support responsive table
        $sTxt = preg_replace("/<table([^\>]*)>/uim", "<div class=\"table-wrapper table-responsive\"><table $1>", $sTxt);
        $sTxt = preg_replace("/<\/table>/uim", "</table></div>", $sTxt);

        if($bParseNewLine) {
            $sTxt = str_replace("\n", "<div class=\"newline\"></div>", $sTxt);
        }

		$sTxt = $this->replaceUserTag($sTxt);
		$sTxt = trim($sTxt);
		return $sTxt;
	}

	/**
	 * @param $str
	 * @return \Api\User\Object[]
	 */
	public function mentionsRegex($str) {
		$return = [];
		preg_match_all($this->_regex['mentions'], $str, $matches);
        $users = implode(',', array_unique($matches[1]));
		if ($users) {
            $search = Phpfox_Database::instance()->select('*')->from(':user')->where(['user_id' => ['in' => $users]])->all();
			foreach ($search as $user) {
				$return[] = new \Api\User\Object($user);
			}
		}

		return $return;
	}

	private function _clean($str) {
		$str = strip_tags($str);
		$str = str_replace(array('"', "'", ' '), '', $str);
		return $str;
	}

	private function _replaceHashTags($aMatches)
	{
	    // check user tagged
        preg_match("/\[user=(\d+)\].+?\[\/user\]/iu", $aMatches[0], $aMatcheUserTaggeds);
        if(is_array($aMatcheUserTaggeds) && count($aMatcheUserTaggeds))
            return $aMatches[0];

		$sTagSearch = substr_replace(strip_tags($aMatches[0]), '', 0, 1);
		$sTagSearch = preg_replace('/\[UNICODE\]([0-9]+)\[\/UNICODE\]/', '&#\\1;', $sTagSearch);
		$sTagSearch = html_entity_decode($sTagSearch, null, 'UTF-8');
		$sTagSearch = urlencode($sTagSearch);

		$sTxt = '<a href="' . Phpfox_Url::instance()->makeUrl('hashtag', array($sTagSearch)) . '" class="site_hash_tag">' . strip_tags($aMatches[0]) . '</a>';

		return $sTxt;
	}
	
	private function _replaceHexColor($aMatches)
	{
		// after change to check whether "color" is in the string or not, this if is incorrect
		if(strlen($aMatches[2]) == 3) {
			$r = hexdec(substr($aMatches[2],0,1).substr($aMatches[2],0,1));
			$g = hexdec(substr($aMatches[2],1,1).substr($aMatches[2],1,1));
			$b = hexdec(substr($aMatches[2],2,1).substr($aMatches[2],2,1));
		}
		else
		{
			$r = hexdec(substr($aMatches[2],0,2));
			$g = hexdec(substr($aMatches[2],2,2));
			$b = hexdec(substr($aMatches[2],4,2));
		}
		$sRGB = "rgb(" . $r . "," . $g . ",". $b . ")";
		
		return " color:" . $sRGB;
	}
	
	public function _replaceUnicode($aMatches)
	{
		return '[UNICODE]' . (int) str_replace(array('&#', ';'), '', $aMatches[0]) . '[/UNICODE]';
	}

	public function cleanTag($sTxt)
    {
        $sTxt = strip_tags($sTxt);
//        $sTxt = preg_replace("/(\W)/ui", ' ', $sTxt);
//        $sTxt = trim(preg_replace("/(\s+)/", ' ', $sTxt));
        return $sTxt;
    }

    public function cleanScriptTag($sTxt)
    {
        $sTxt = preg_replace("/<script([^\>]*)>/uim", "&lt;script$1&gt;", $sTxt);
        $sTxt = preg_replace("/<\/script>/uim", "&lt;/script$1&gt;", $sTxt);
        return $sTxt;
    }

	public function replaceHashTags($sTxt)
	{
		$sTxt = preg_replace_callback("/<a.*?<\/a>(*SKIP)(*F)|(&#+[0-9+]+;)/", array($this, '_replaceUnicode'), $sTxt);
		$sTxt = preg_replace_callback("/<a.*?<\/a>(*SKIP)(*F)|(\-?color:)\s*#([A-F0-9]{3,6})/i", array($this, '_replaceHexColor'), $sTxt);
		$sTxt = preg_replace_callback("/<a.*?<\/a>(*SKIP)(*F)|(#[\w]+)/iu", array($this, '_replaceHashTags'), $sTxt);
		$sTxt = preg_replace('/\[UNICODE\]([0-9]+)\[\/UNICODE\]/', '&#\\1;', $sTxt);
		return $sTxt;
	}

	public function getHashTags($sTxt)
	{
		$aTags = array();
		$sTxt = str_replace(array('<br >', '<br />', '<p>', '</p>'), ' ', $sTxt);
		
		$sTxt = preg_replace_callback("/(&#+[0-9+]+;)/", array($this, '_replaceUnicode'), $sTxt);
		
		$sTxt = preg_replace("/#([A-F0-9]{6})/i", "", $sTxt);
		$sTxt = preg_replace("/(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/i", "", $sTxt);

        preg_match_all("/(#\w+)/iu", $sTxt, $aMatches);

        if (is_array($aMatches) && count($aMatches))
		{
			foreach ($aMatches as $aSubTags)
			{
				foreach ($aSubTags as $sTag)
				{
					$sTag = preg_replace('/\[UNICODE\]([0-9]+)\[\/UNICODE\]/', '&#\\1;', $sTag);

					// check user tagged
                    preg_match("/\[user=(\d+)\].+?\[\/user\]/iu", $sTag, $aMatcheUserTaggeds);
					if(is_array($aMatcheUserTaggeds) && count($aMatcheUserTaggeds))
					    continue;

					$aTags[] = substr_replace($sTag, '', 0, 1);
				}

				break;
			}
		}
		return $aTags;
	}

	public function parseUrls($sTxt)
	{
		if (Phpfox::getParam('core.disable_all_external_emails'))
		{
			$sTxt = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', array(&$this, '_replaceEmails'), $sTxt);	
		}
		
		if (Phpfox::getParam('core.disable_all_external_urls'))
		{
			$sTxt = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', array(&$this, '_replaceLinks'), $sTxt);
			$sTxt = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', array(&$this, '_replaceLinks'), $sTxt);	
		}

		if (Phpfox::getParam('core.warn_on_external_links'))
		{
			$sTxt = preg_replace_callback('/<a\s(.*?)>/i', array(&$this, '_warnOnExtLink'), $sTxt);
		}

		$sTxt = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', array(&$this, '_urlToLink'), $sTxt);
		$sTxt = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', array(&$this, '_urlToLink'), $sTxt);
		
		if (Phpfox::getParam('core.no_follow_on_external_links'))
		{
			$sTxt = preg_replace('/<a\s(.*?)>/is', '<a \\1 rel="nofollow">', $sTxt);
		}		
		
		$sTxt = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $sTxt);
		$sTxt = trim($sTxt);

		return $sTxt;
	}
	
	private function _warnOnExtLink($aMatches)
	{
		if (!isset($aMatches[1]))
		{
			return '';
		}
		
		$sHref = '';
		$aParts = explode(' ', $aMatches[1]);
		foreach ($aParts as $sPart)
		{
			if (substr($sPart, 0, 5) == 'href=')
			{
				$sHref = $sPart;
				if (!preg_match('/' . preg_quote(Phpfox::getParam('core.host')) . '/i', $sHref))
				{
					$sHref = str_replace(array('"', "'"), '', $sHref);
					$sHref = substr_replace($sHref, '', 0, 5);
					$sHref = 'href="' . Phpfox_Url::instance()->makeUrl('core.redirect', array('url' => Phpfox_Url::instance()->encode($sHref))) . '"';
				}
				
				return '<a ' . $sHref . '>';
				break;
			}
		}
		
		if (empty($sHref))
		{
			return '<a ' . $aMatches[1] . '>';
		}
	}
	
	public function parseUserTagged($iUser)
	{
		return $this->_parseUserTagged($iUser);
	}
	
	/**
	* Parses users from tags by querying the DB and getting their full name.
	*/
	private function _parseUserTagged($iUser)
	{
		if (is_array($iUser)) $iUser = $iUser[1];

		static $aCache = array();

		if (!isset($aCache[$iUser]))
		{
			$oDb = Phpfox_Database::instance();
	
			$aUser = $oDb->select('up.user_value, u.full_name, user_name')
				->from(Phpfox::getT('user'), 'u')
				->leftJoin(Phpfox::getT('user_privacy'), 'up', 'up.user_id = u.user_id AND up.user_privacy = \'user.can_i_be_tagged\'')
				->where('u.user_id = ' . (int) $iUser)
				->execute('getSlaveRow');

			$sOut = '';
			if (isset($aUser['user_value']) && !empty($aUser['user_value']) && $aUser['user_value'] > 2)
			{
				$sOut = $aUser['full_name'];
			}
			else
			{
				if (isset($aUser['user_name']))
				{
					$sOut = '<a class="test" id="'.json_encode($iUser).'" href="' . Phpfox_Url::instance()->makeUrl($aUser['user_name']) .'">' . $aUser['full_name'] .'</a>';
				}
			}
			
			$aCache[$iUser] = $sOut;
			
			return $sOut;
		}
		else
		{
			return $aCache[$iUser];
		}
	}
	
	public function feedStrip($sStr)
	{
		return $this->parse(strip_tags($sStr), false);
	}
	
	public function maxLine($sStr)
	{
		if (!$this->isShortened() && Phpfox::getParam('core.activity_feed_line_breaks') > 0)
		{
			$aLines = explode("<br />", $sStr);
			if (count($aLines) > Phpfox::getParam('core.activity_feed_line_breaks'))
			{
				$sLines = '<span class="js_read_more_parent_main">';
				$iLineCnt = 0;
				foreach ($aLines as $sLine)
				{
					$iLineCnt++;
						
					if ($iLineCnt > Phpfox::getParam('core.activity_feed_line_breaks'))
					{
						$this->_bIsMaxLine = true;
						$sLines .= '<span class="js_read_more_parent" style="display:none;">';
						$sLines .= $sLine . '<br />';
						$sLines .= '</span>';
					}
					else
					{
						$sLines .= $sLine . '<br />';
					}
				}
		
				if (isset($this->_bIsMaxLine))
				{
					$sLines .= '<div><a href="#" onclick="$(this).parents(\'.js_read_more_parent_main:first\').find(\'.js_read_more_parent\').show(); $(this).hide(); return false;">' . _p('view_more') . '</a></div>';
				}
		
				$sLines .= '</span>';
		
				return $sLines;
			}
		}
		
		return $sStr;
	}
	
	public function replaceUserTag($sStr)
	{
		$sStr = preg_replace_callback('/\[user=(\d+)\].+?\[\/user\]/u', array($this, '_parseUserTagged'), $sStr);
		return $sStr;
	}
	
	/**
	 * Set image parser settings.
	 *
	 * @param array $aParams ARRAY of settings.
	 */
	public function setImageParser($aParams)
	{
		if (isset($aParams['clear']))
		{
			$this->_aImageParams = array();
		}
		else 
		{
			$this->_aImageParams = $aParams;
		}
	}
	
	/**
	 * Set video embed settings.
	 *
	 * @param array $aParams ARRAY of settings.
	 */
	public function setEmbedParser($aParams = null)
	{
		if ($aParams == null)
		{
			$this->_aEmbedParams = array('width' => Phpfox::getParam('feed.width_for_resized_videos'), 'height' => Phpfox::getParam('feed.height_for_resized_videos'));
		}
		elseif (isset($aParams['clear']))
		{
			$this->_aEmbedParams = array();
		}
		else 
		{
			$this->_aEmbedParams = $aParams;
		}
	}	
	
	/**
	 * Clean input text, usually used within HTML <input>
	 *
	 * @param string $sTxt is the string we need to clean
	 * @param bool $bHtmlChar TRUE to convert HTML characters or FALSE to not convert.
	 * @return string Cleaned string
	 */
	public function clean($sTxt, $bHtmlChar = true)
	{			
		if (!defined('PHPFOX_INSTALLER'))
		{
			$sTxt = Phpfox::getService('ban.word')->clean($sTxt);
		}
		
		$sTxt = ($bHtmlChar ? $this->htmlspecialchars($sTxt) : $sTxt);
		
		$sTxt = str_replace('&#160;', '', $sTxt);
		return $sTxt;
	}
	
	/**
	 * Our method of PHP htmlspecialchars().
	 *
	 * @see htmlspecialchars()
	 * @param string $sTxt String to convert.
	 * @return string Converted string.
	 */
	public function htmlspecialchars($sTxt)
	{
		$sTxt = preg_replace('/&(?!(#[0-9]+|[a-z]+);)/si', '&amp;', $sTxt);
		$sTxt = str_replace(array(
			'"',
			"'",
			'<',
			'>'
		),
		array(
			'&quot;',
			'&#039;',
			'&lt;',
			'&gt;'
		), $sTxt);		
		
		return $sTxt;
	}

	/**
	 * Clean text when being sent back via AJAX. 
	 * Usually this is used to send back to an HTML <textarea>
	 *
	 * @param string $sTxt is the text we need to clean
	 * @return string Cleaned Text
	 */
	public function ajax($sTxt)
	{		
		$sTxt = Phpfox::getService('ban.word')->clean($sTxt);
		$sTxt = str_replace("\r", "", $sTxt);

		return $sTxt;
	}

	public function truncate($text, $length){
        $html  = true;
        $ending = '';
        $exact = true;

        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $totalLength = mb_strlen(strip_tags($ending));
            $openTags = array();
            $truncate = '';

            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
            }
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($html) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;

        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</'.$tag.'>';
            }
        }

        return $truncate;
    }
	
	/**
	 * Shortens a string.
	 *
	 * @param string $html String to shorten.
	 * @param int $maxLength Max length.
	 * @param string $sSuffix Optional suffix to add.
	 * @param bool $bHide TRUE to hide the shortened string, FALSE to remove it.
	 * @return string Returns the new shortened string.
	 */
    public function shorten($html, $maxLength, $sSuffix = null, $bHide = false)
    {
        mb_internal_encoding('UTF-8');

        if (defined('PHPFOX_LANGUAGE_SHORTEN_BYPASS') || $maxLength === 0 || $this->hasReachedMaxLine() || Phpfox::getParam('language.no_string_restriction'))
        {
            return $html;
        }

        $sNewString = $this->truncate($html, $maxLength)   ;

        // fix issue emoji
        $sNewString = preg_replace('/(:{1}[\w]*)$/', '', $sNewString);

		if ($sSuffix !== null)
		{
		    $sCountHtml = strip_tags($html);
		    $sCountHtml = preg_replace('/&#?[a-zA-Z0-9]+;/i', 'A', $sCountHtml);			
			
			if (strlen($sCountHtml) > $maxLength)
			{
                $sSuffix = _p($sSuffix);
				$this->_bIsShortened = true;
				
				if ($bHide === true)
				{
					if (defined('PHPFOX_IS_THEATER_MODE'))
					{

						$sNewString = '<span class="js_view_more_parent"><span class="js_view_more_part">' . $this->closeAllHtmlTags($sNewString) . '...<span class="item_view_more"><a href="#" onclick="$(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_part\').hide(); $(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_full\').show(); return false;">' . $sSuffix . '</a></span></span>';
				    	$sNewString .= '<span class="js_view_more_full" style="display:none; position:absolute; z-index:10000; background:#fff; border:1px #dfdfdf solid;">';
				    	$sNewString .= '<div style="max-height:200px; overflow:auto; padding:5px;">' . $html . '</div>';
                        $sNewString .= '<div class="item_view_more" style="padding:10px; text-align:center;"><a href="#" onclick="$(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_full\').hide(); $(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_part\').show(); return false;">' . _p('view_less'). '</a></div>';
				    	$sNewString .= '</span>';
				    	$sNewString .= '</span>';
					}
					else
					{
						$sNewString = '<span class="js_view_more_parent"><span class="js_view_more_part">' . $this->closeAllHtmlTags($sNewString) . '...<span class="item_view_more"><a href="#" onclick="$(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_part\').hide(); $(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_full\').show(); return false;">' . $sSuffix . '</a></span></span>';
				    	$sNewString .= '<span class="js_view_more_full" style="display:none;">';
				    	$sNewString .= $html;
                        $sNewString .= '<div class="item_view_more"><a href="#" onclick="$(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_full\').hide(); $(this).parents(\'.js_view_more_parent:first\').find(\'.js_view_more_part\').show(); return false;">' . _p('view_less'). '</a></div>';
                        $sNewString .= '</span>';
				    	$sNewString .= '</span>';
					}
				}
				else 
				{
					$sNewString .= $sSuffix;
				}
			}
		}
		
    	return $sNewString;    	
    }   
    
    /**
     * Return if the last string we checked was shortened.
     *
     * @return bool TRUE it was shortened, FALSE if was not.
     */
    public function isShortened()
    {
    	$bLastCheck =  $this->_bIsShortened;
    	
    	$this->_bIsShortened = false;
    	
    	return $bLastCheck;
    }
    
    /**
     * Return if the last string we checked reached the max number of lines.
     *
     * @return bool TRUE it was reached, FALSE if was not.
     */
    public function hasReachedMaxLine()
    {
    	$bLastCheck =  $this->_bIsMaxLine;
    	
    	$this->_bIsMaxLine = false;
    	
    	return $bLastCheck;
    }
    
    /**
     * Split a string at a specified location. This allows for browsers to
     * automatically add breaks or wrap long text strings.
     *
     * @param string $sString Text string you want to split.
     * @param int $iCount How many characters to wait until we need to perform the split.
     * @param bool $bBreak FALSE will just add a space and TRUE will add an HTML <br />.
     * @return string Converted string with splits included.
     */
    public function split($sString, $iCount, $bBreak = false)
    {
    	if ($sString == '0' || Phpfox::getParam('language.no_string_restriction')) {
			return $sString;
		}
    	$sNewString = '';
   		$aString = explode('>', $sString);
   		$iSizeOf = sizeof($aString);
   		$bHasNonAscii = false;
   		for ($i=0; $i < $iSizeOf; ++$i) 
   		{
        	$aChar = explode('<', $aString[$i]);        	
            	
       		if (!empty($aChar[0])) 
       		{	       			
       			if (preg_match('/&#?[a-zA-Z0-9]+;/', $aChar[0]))
       			{
	       		 	$aChar[0] = str_replace('&lt;', '[PHPFOX_START]', $aChar[0]);
	       			$aChar[0] = str_replace('&gt;', '[PHPFOX_END]', $aChar[0]);
       				$aChar[0] = html_entity_decode($aChar[0], null, 'UTF-8');
	       					
	       			$bHasNonAscii = true;
       			}       			
       			if ($iCount > 9999)
				{
					$iCount = 9999;
				}
       			$sNewString .= preg_replace('#([^\n\r(?!PHPFOX_) ]{'. $iCount .'})#iu', '\\1 ' . ($bBreak ? '<br />' : ''), $aChar[0]);
       		}
       	
       		if (!empty($aChar[1])) 
       		{
           		$sNewString .= '<' . $aChar[1] . '>';
       		}
   		}   
		
   		$sOut = ($bHasNonAscii === true ? str_replace(array('[PHPFOX_START]', '[PHPFOX_END]'), array('&lt;', '&gt;'), Phpfox::getLib('parse.input')->convert($sNewString)) : $sNewString);
		
   		return $sOut;
    }

    public function closeAllHtmlTags($html) {

        #put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];   #put all closed tags into an array

        preg_match_all('#</([a-z]+)>#iU', $html, $result);

        $closedtags = $result[1];

        $len_opened = count($openedtags);

        # all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }

        $openedtags = array_reverse($openedtags);

        # close tags
        for ($i=0; $i < $len_opened; $i++) {

            if (!in_array($openedtags[$i], $closedtags)){

                $html .= '</'.$openedtags[$i].'>';

            } else {

                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }

        return $html;
    }

/**
     * Replace unwanted emails on the site. We also take into account emails
     * that are added into the "white" list.
     *
     * @param array $aMatches ARRAY matches from preg_match.
     * @return string Returns replaced emails.
     */
	private function _replaceEmails($aMatches)
	{
		static $aSites = null;
		
		if ($aSites === null)
		{
			$aSites = explode(',', trim(Phpfox::getParam('core.email_white_list')));
		}
		
		foreach ($aSites as $sSite)
		{
			$sSite = trim($sSite);
			$sSite = str_replace(array('.', '*'), array('\.', '(.*?)'), $sSite);
					
			if (preg_match('/' . $sSite . '/is', $aMatches[0]))
			{
				return $aMatches[0];
			}
		}
	}
	
    /**
     * Replace unwanted links on the site. We also take into account links
     * that are added into the "white" list.
     *
     * @param array $aMatches ARRAY matches from preg_match.
     * @return string Returns replaced links.
     */
	private function _replaceLinks($aMatches)
	{
		static $aSites = null;
		
		if ($aSites === null)
		{
			$aSites = explode(',', trim(Phpfox::getParam('core.url_spam_white_list')) . ',' . Phpfox::getParam('core.host'));
		}
		
		foreach ($aSites as $sSite)
		{
			$sSite = trim($sSite);
			$sSite = str_replace(array('.', '*'), array('\.', '(.*?)'), $sSite);
					
			if (preg_match('/' . str_replace('/','\/',$sSite) . '/is', $aMatches[0]))
			{
				return $aMatches[0];
			}
		}
	}
    
	/**
	 * Converts a URL into a HTML anchor.
	 *
	 * @param array $aMatches ARRAY matches from preg_match.
	 * @return string Converted URL.
	 */
    private function _urlToLink($aMatches)
    {    	
		$aMatches[2] = trim($aMatches[2]);

		if (empty($aMatches[2]))
		{
			return '';
		}
		
		$sHref = $aMatches[2];
		
		if ($sHref == 'ftp.')
		{
			return ' ' . $sHref;	
		}
		
		if (!preg_match("/^(http|https|ftp):\/\/(.*?)$/i", $sHref))
		{
			$sHref = 'http://' . $sHref;
		}		

		$sName = $aMatches[2];		
		if (Phpfox::getParam('core.shorten_parsed_url_links') > 0)
		{
			$sName = substr($sName, 0, Phpfox::getParam('core.shorten_parsed_url_links')) . (strlen($sName) > Phpfox::getParam('core.shorten_parsed_url_links') ? '...' : '');
		}		

		if (Phpfox::getParam('core.warn_on_external_links'))
		{
			if (!preg_match('/' . preg_quote(Phpfox::getParam('core.host')) . '/i', $sHref))
			{
				$sHref = Phpfox_Url::instance()->makeUrl('core.redirect', array('url' => Phpfox_Url::instance()->encode($sHref)));
			}
		}

		
		return $aMatches[1] . "<a href=\"" . $sHref . "\" target=\"_blank\" rel=\"nofollow\">" . $sName . "</a> ";
    }
    
    /**
     * Gets a phrase from the language package.
     *
     * @param string $aMatches ARRAY matches from preg_match.
     * @return string Returns the phrase if we can find it.
     */
    private function _getPhrase($aMatches)
    {
    	return (isset($aMatches[1]) ? _p($aMatches[1]) : $aMatches[0]);
    }
    
    /**
     * Fixes image widths.
     *
     * @param array $aMatches ARRAY of matches from a preg_match.
     * @return string Returns the image with max-width and max-height included.
     */
    private function _fixImageWidth($aMatches)
    {
    	$aParts = Phpfox::getLib('parse.input')->getParams($aMatches[1]);
    	$iWidth = (isset($this->_aImageParams['width']) ? (int) $this->_aImageParams['width'] : 400);
    	$iHeight = (isset($this->_aImageParams['height']) ? (int) $this->_aImageParams['height'] : 400);

    	(($sPlugin = Phpfox_Plugin::get('parse_output_fiximagewidth')) ? eval($sPlugin) : false);
    	
    	return '<img style="max-width:' . $iWidth . 'px; max-height:' . $iHeight . '" ' . $aMatches[1] . '>';
    }
}