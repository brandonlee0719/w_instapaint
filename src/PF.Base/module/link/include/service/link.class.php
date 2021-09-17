<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

use MediaEmbed\MediaEmbed;

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Service
 * @version         $Id: link.class.php 7240 2014-03-31 15:22:15Z Fern $
 */
class Link_Service_Link extends Phpfox_Service
{
    /**
     * Class constructor
     */

    /**
     * @var string
     */
    private $_sYouTubeApiKey = 'AIzaSyBYr7IzpV5nW96aI-ToXulXWDrEfw2qPsA';

    public $_sTable;


    public function __construct()
    {
        $this->_sTable = Phpfox::getT('link');
        if ($sYTApi = Phpfox::getParam('link.youtube_data_api_key')) {
            if (count($sYTApi) > 12) {
                $this->_sYouTubeApiKey = $sYTApi;
            }
        }
    }

    private function get_remote_contents($sUrl)
    {
        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    private function parseYouTubeUrl($url)
    {
        $aReturn = false;
        $pattern = '%^(?:https?://)?(?:www\.)?(?:youtu\.be/| youtube\.com(?:/embed/| /v/| /watch\?v=))([\w-]{10,12})$%x';
        if (preg_match($pattern, $url, $match)) {
            $json = fox_get_contents('https://www.googleapis.com/youtube/v3/videos?id=' . $match[1] . '&key=' . $this->_sYouTubeApiKey  . '&part=snippet,contentDetails');
            $oYTData = json_decode($json);
            $start = new DateTime('@0'); // Unix epoch
            $start->add(new DateInterval($oYTData->items[0]->contentDetails->duration));
            $duration = $start->format('H')*60*60 + $start->format('i')*60 + $start->format('s');
            $aReturn = [
                'title' => $oYTData->items[0]->snippet->title,
                'image' => $oYTData->items[0]->snippet->thumbnails->default->url,
                'description' => $oYTData->items[0]->snippet->description,
                'duration' => sprintf("%s", $duration)
            ];

        }
        return $aReturn;
    }

    public function getLink($sUrl)
    {
        if (substr($sUrl, 0, 7) != 'http://' && substr($sUrl, 0, 8) != 'https://') {
            $sUrl = 'http://' . $sUrl;
        }

        $aParts = parse_url($sUrl);

        if (!isset($aParts['host'])) {
            return Phpfox_Error::set(_p('not_a_valid_link'), true);
        }
        
        $aParseBuild = array();
        if ($aYT = $this->parseYouTubeUrl($sUrl)) {
            $aParseBuild['title'] = $aYT['title'];
            $aParseBuild['description'] = $aYT['description'];
            $aParseBuild['image'] = $aYT['image'];
            $aParseBuild['duration'] = $aYT['duration'];
        } elseif (class_exists('DOMDocument')) {
            $doc = new DOMDocument("1.0", 'utf-8');
            $html = $this->get_remote_contents($sUrl);

            // now we inject another meta tag
            $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
            $html = str_replace('<head>', '<head>' . $contentType, $html);
            @$doc->loadHTML($html);
            $titleList = $doc->getElementsByTagName("title");
            $metaList = $doc->getElementsByTagName("meta");
            foreach ($metaList as $iKey => $meta) {
                $type = $meta->getAttribute('property');
                $content = $meta->getAttribute('content');
                $aParseBuild[$type] = $content;
            }
            if ($titleList->length > 0) {
                $aParseBuild['title'] = $titleList->item(0)->nodeValue;
            } else {
                $aParseBuild['title'] = '';
            }
        } else {
            $sContent = Phpfox_Request::instance()->send($sUrl, array(), 'GET', $_SERVER['HTTP_USER_AGENT'], null,
                true);
            preg_match_all('/<(meta|link)(.*?)>/i', $sContent, $aRegMatches);
            if (preg_match('/<title>(.*?)<\/title>/is', $sContent, $aMatches)) {
                $aParseBuild['title'] = $aMatches[1];
            } else {
                if (preg_match('/<title (.*?)>(.*?)<\/title>/is', $sContent, $aMatches) && isset($aMatches[2])) {
                    $aParseBuild['title'] = $aMatches[2];
                }
            }

            if (isset($aRegMatches[2])) {
                foreach ($aRegMatches as $iKey => $aMatch) {
                    if ($iKey !== 2) {
                        continue;
                    }

                    foreach ($aMatch as $sLine) {
                        $sLine = rtrim($sLine, '/');
                        $sLine = trim($sLine);

                        preg_match('/(property|name|rel|image_src)=("|\')(.*?)("|\')/is', $sLine, $aType);
                        if (count($aType) && isset($aType[3])) {
                            $sType = $aType[3];
                            preg_match('/(content|type)=("|\')(.*?)("|\')/i', $sLine, $aValue);
                            if (count($aValue) && isset($aValue[3])) {
                                if ($sType == 'alternate') {
                                    $sType = $aValue[3];
                                    preg_match('/href=("|\')(.*?)("|\')/i', $sLine, $aHref);
                                    if (isset($aHref[2])) {
                                        $aValue[3] = $aHref[2];
                                    }
                                }
                                $aParseBuild[$sType] = $aValue[3];
                            }
                        }
                    }
                }
            }
        }
        $image = '';
        $embed = '';
        $MediaEmbed = new MediaEmbed();
        $MediaObject = $MediaEmbed->parseUrl($sUrl);
        if (!$MediaObject instanceof \MediaEmbed\Object\MediaObject) {
            if (isset($aParseBuild['og:image'])) {
                $image = $aParseBuild['og:image'];
            }
        } else {
            $image = $MediaObject->image();
            $embed = $MediaObject->getEmbedCode();
        }

        preg_match('/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/',
            $sUrl, $aFbVideo);
        if (count($aFbVideo)) {
            try {
                $graphApiUrl = "https://graph.facebook.com/$aFbVideo[2]?fields=picture";
                $graphAptResult = fox_get_contents($graphApiUrl);
                $graphAptResult = json_decode($graphAptResult);
                $image = $graphAptResult->picture;
            } catch (Exception $e) {
                $image = '';
            }
        }

        if (!$embed) {
            if (isset($aParseBuild['application/json+oembed'])) {
                stream_context_create(
                    [
                        'http' => [
                            'header'     => 'Connection: close',
                            'user_agent' => $_SERVER['HTTP_USER_AGENT']
                        ]
                    ]);
                $source = json_decode(preg_replace('/[^(\x20-\x7F)]*/', '',
                    fox_get_contents($aParseBuild['application/json+oembed'])));
                if (isset($source->html)) {
                    $id = str_replace('fb://photo/', '', $aParseBuild['al:android:url']);
                    $image = 'https://graph.facebook.com/' . $id . '/picture';
                    $embed = '<div class="fb_video_iframe"><iframe src="https://www.facebook.com/video/embed?video_id=' . $id . '"></iframe></div>';
                }
            }
        }

        if (isset($aParseBuild['title'])) {
            $aParseBuild['og:title'] = $aParseBuild['title'];
            if (isset($aParseBuild['description'])) {
                $aParseBuild['og:description'] = $aParseBuild['description'];
            }
        }

        if (!$image && isset($aParseBuild['og:image'])) {
            $image = $aParseBuild['og:image'];
        }

        $image = Phpfox::getLib('url')->secureUrl($image);
        $aParts = parse_url($sUrl);
        $aReturn = [
            'link'          => $sUrl,
            'title'         => (isset($aParseBuild['og:title']) ? $aParseBuild['og:title'] : ''),
            'description'   => (isset($aParseBuild['og:description']) ? $aParseBuild['og:description'] : ''),
            'duration'      => (isset($aParseBuild['duration'])) ? $aParseBuild['duration'] : '',
            'default_image' => $image,
            'embed_code'    => $embed,
            'host'          => $aParts['host'],
        ];
        return $aReturn;
    }

    public function getEmbedCode($iId, $bIsPopUp = false)
    {
        $aLinkEmbed = $this->database()->select('embed_code')
            ->from(Phpfox::getT('link_embed'))
            ->where('link_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $iWidth = 640;
        $iHeight = 390;
        if (!$bIsPopUp) {
            $iWidth = 480;
            $iHeight = 295;
        }

        $aLinkEmbed['embed_code'] = preg_replace('/width=\"(.*?)\"/i', 'width="' . $iWidth . '"',
            $aLinkEmbed['embed_code']);
        $aLinkEmbed['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="' . $iHeight . '"',
            $aLinkEmbed['embed_code']);
        $aLinkEmbed['embed_code'] = str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'),
            $aLinkEmbed['embed_code']);

        if (Phpfox::getParam('core.force_https_secure_pages')) {
            $aLinkEmbed['embed_code'] = str_replace('http://', 'https://', $aLinkEmbed['embed_code']);
        }

        return $aLinkEmbed['embed_code'];
    }

    public function getLinkById($iId)
    {
        $aLink = $this->database()->select('l.*, u.user_name')
            ->from(Phpfox::getT('link'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where('l.link_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aLink['link_id'])) {
            return false;
        }

        return $aLink;
    }

    /**
     * @deprecated This function will be removed in 4.6.0
     * @param $aItem
     * @return array|int|string
     */
    public function getInfoForAction($aItem)
    {
        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        }
        $aRow = $this->database()->select('l.link_id, l.title, l.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('link'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where('l.link_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        $aRow['link'] = Phpfox_Url::instance()->permalink('link', $aRow['link_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * Get url to a specific link
     * @param $iLinkId
     * @return bool|string
     */
    public function getUrl($iLinkId)
    {
        $iUserId = db()->select('user_id')->from(':link')->where(['link_id' => $iLinkId])->executeField();

        if (!$iUserId) {
            return '';
        }

        return Phpfox::getService('user')->getLink($iUserId, null, ['link-id' => $iLinkId]);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('link.service_link__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}