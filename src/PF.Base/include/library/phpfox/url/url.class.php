<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * URL
 * Class is used to build the URL structure of the site.
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: url.class.php 7062 2014-01-22 19:16:20Z Fern $
 */
class Phpfox_Url
{
    /**
     * List of rewrite rules.
     *
     * @var array
     */
    public $aRewrite = [];

    /**
     * List of rewrite rules in reverse
     *
     * @var array
     */
    public $aReverseRewrite = [];

    /**
     * List of all the requests.
     *
     * @var array
     */
    private $_aParams = [];

    /**
     * Not being used.
     *
     * @deprecated 2.0.5
     * @var bool
     */
    private $_bIsUsed = false;

    /**
     * List of headers
     *
     * @var array
     */
    protected $_aHeaders = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out",
    ];

    /**
     * @var int
     */
    private $_bUrlRewrite =  1;

    /**
     * @var string
     */
    private $_sPath =  '';

    /**
     * @var string
     */
    private $_sCoreFolder = '';

    /**
     * @var string
     */
    private $_sCoreModuleCore = '';

    /**
     * @var bool
     */
    private $_bForceSecurePages = false;

    /**
     * @var string
     */
    private $_sPluginCheckUrlIsArray =  null;

    /**
     * @var string
     */
    private $_sCheckUrlIsArrayReturn;

    /**
     * Class constructor is used to build the current URL and all the custom rewrite rules.
     *
     */
    public function __construct()
    {
        if (!defined('PHPFOX_INSTALLER')) {
            $oCache = Phpfox::getLib('cache');
            $iCacheId = $oCache->set('rewrite');

            $iReverseCacheId = $oCache->set('rewrite_reverse');

            if ((!($this->aRewrite = $oCache->get($iCacheId))) || (!($this->aReverseRewrite = $oCache->get($iReverseCacheId)))) {
                $aRows = Phpfox_Database::instance()->select('r.url, r.replacement')
                    ->from(Phpfox::getT('rewrite'), 'r')
                    ->execute('getRows');
                foreach ($aRows as $aRow) {
                    $aParts = explode('/', $aRow['url']);
                    $this->aRewrite[ $aRow['url'] ] = $aRow['replacement'];
                    $this->aReverseRewrite[ rtrim($aRow['replacement'], '/') ] = $aRow['url'];
                }

                $oCache->save($iCacheId, $this->aRewrite);
                $oCache->save($iReverseCacheId, $this->aReverseRewrite);
                Phpfox::getLib('cache')->group('url', $iCacheId);
                Phpfox::getLib('cache')->group('url', $iReverseCacheId);
            }

            $this->_bUrlRewrite = Phpfox::getParam('core.url_rewrite');
            $this->_sPath =  Phpfox::getParam('core.path');
            $this->_sCoreFolder =  Phpfox::getParam('core.folder');
            $this->_sCoreModuleCore =  Phpfox::getParam('core.module_core');
            $this->_bForceSecurePages =  Phpfox::getParam('core.force_https_secure_pages');
            $this->_sPluginCheckUrlIsArray = Phpfox_Plugin::get('check_url_is_array');
            $this->_sCheckUrlIsArrayReturn =  Phpfox_Plugin::get('check_url_is_array_return');
        }

        $this->_setParams();
    }

    /**
     * @return Phpfox_Url
     */
    public static function instance()
    {
        return Phpfox::getLib('url');
    }

    public function getHeaderCode($iCode)
    {
        if (isset($this->_aHeaders[ $iCode ])) {
            return $this->_aHeaders[ $iCode ];
        }

        return null;
    }

    /**
     * Encodes a URL string.
     *
     * @param string $sStr URL string.
     *
     * @return string URL encoded string.
     */
    public function encode($sStr)
    {
        $sStr = serialize($sStr);

        if (function_exists('gzcompress')) {
            $sStr = gzcompress($sStr, 9);
        }

        return strtr(base64_encode(addslashes($sStr)), '+/=', '-_,');
    }

    /**
     * Decodes a URL string encoded with the method encode().
     *
     * @see self::encode()
     *
     * @param string $sStr URL string to decode.
     *
     * @return string Decoded URL string.
     */
    public function decode($sStr)
    {
        $sStr = stripslashes(base64_decode(strtr($sStr, '-_,', '+/=')));

        if (function_exists('gzuncompress')) {
            $sStr = gzuncompress($sStr);
        }

        return unserialize($sStr);
    }

    /**
     * Get all the custom rewrite rules.
     *
     * @return array
     */
    public function getRewrite()
    {
        return $this->aRewrite;
    }

    /**
     * Perform a URL rewrite if it exists.
     *
     * @param string $sUrl URL to write.
     *
     * @return string If rewrite exists it will return the rewrite value.
     */
    public function doRewrite($sUrl)
    {
        if (isset($this->aRewrite[ $sUrl ])) {
            if (is_array($this->aRewrite[ $sUrl ]) && isset($this->aRewrite[ $sUrl ]['component'])) {
                return $this->aRewrite[ $sUrl ]['component'];
            }

            return $this->aRewrite[ $sUrl ];
        }

        return $sUrl;
    }

    /**
     * Perform a URL reverse rewrite if it exists.
     *
     * @param string $sUrl URL to check if there is a rewrite and then to reverse it.
     *
     * @return string Rewritten URL.
     */
    public function reverseRewrite($sUrl)
    {
        if (isset($this->aReverseRewrite[ $sUrl ])) {
            return $this->aReverseRewrite[ $sUrl ];
        }

        return $sUrl;
    }

    /**
     * Send the user to a new page. Works similar to PHP "header('Location: ...');".
     *
     * @param string $sUrl URL.
     * @param string $sMsg Optional message you can pass which will be displayed on the arrival page.
     */
    public function forward($sUrl, $sMsg = '', $iHeader = null)
    {
        if ($sMsg) {
            Phpfox::addMessage($sMsg);
        }

        $this->_send($sUrl, $iHeader);
        exit;
    }

    /**
     * Send a user to a new page using the URL method we use.
     *
     * @param string $sUrl Internal URL.
     * @param array $aParams ARRAY of params to include in the URL.
     * @param string $sMsg Optional message you can pass which will be displayed on the arrival page.
     * @param null $iHeader
     * @param string $sMessageType
     * @param bool $bMessageAutoClose
     */
    public function send($sUrl, $aParams = null, $sMsg = null, $iHeader = null, $sMessageType = 'success', $bMessageAutoClose = true)
    {
        if ($aParams !== null && is_string($aParams)) {
            $sMsg = $aParams;
            $aParams = [];
        }

        if ($sMsg !== null) {
            Phpfox::addMessage($sMsg, $sMessageType, $bMessageAutoClose);
        }

        //Redirect to invalid page if module subscribe is disabled
        if ($sUrl == 'subscribe' && !Phpfox::isModule('subscribe')) {
            $sUrl = 'privacy.invalid';
        }

        $url = (preg_match("/(http|https):\/\//i", $sUrl) ? $sUrl : $this->makeUrl($sUrl, $aParams));

        $this->_send($url, $iHeader);
        exit;
    }

    /**
     * Checks to see if a URL exists.
     *
     * @param mixed $mName STRING name of URL or ARRAY of URLs to check.
     *
     * @return bool TRUE if URL exists, FALSE if not.
     */
    public function isUrl($mName)
    {
        $sUrl = $this->getUrl();

        if (is_array($mName)) {
            foreach ($mName as $sName) {
                if ($this->_isUrl($sUrl, $sName)) {
                    return true;
                }
            }

            return false;
        }

        return $this->_isUrl($sUrl, $mName);
    }

    /**
     * Get the URL of the current page.
     *
     * @return string URL.
     */
    public function getUrl()
    {
        $sUrl = '';

        foreach ($this->_aParams as $sKey => $sValue) {
            if (substr($sKey, 0, 3) == 'req') {
                $sUrl .= $sValue . '/';
            }
        }
        $sUrl = rtrim($sUrl, '/');

        return $sUrl;
    }

    /**
     * Get the full URL of the current page.
     *
     * @param bool $bNoPath TRUE to include the URL path, FALSE if not.
     *
     * @return string URL.
     */
    public function getFullUrl($bNoPath = false)
    {
        if ($bNoPath) {
            return Phpfox_Request::instance()->get(PHPFOX_GET_METHOD);
        }

        return $this->makeUrl('current');
    }

    /**
     * Clears all params or a single parameter
     *
     * @param string $sName the name of the parameter to clear.
     */
    public function clearParam($mName = '')
    {
        if ($mName) {
            if (!is_array($mName)) {
                $mName = [$mName];
            }

            foreach ($mName as $iKey => $sName) {
                if (!is_numeric($iKey)) {
                    $sName = $iKey;
                }

                unset($_GET[ $sName ]);
                unset($this->_aParams[ $sName ]);
            }
        } else {
            $this->_aParams = [];
        }
    }

    /**
     * Set a request.
     *
     * @param mixed  $mName  STRING request name or ARRAY of requests using keys and values.
     * @param string $sValue Request value only if the 1st argument is a STRING.
     */
    public function setParam($mName, $sValue = '')
    {
        if (!is_array($mName)) {
            $mName = [$mName => $sValue];
        }

        $iReq = 0;
        foreach ($mName as $sName => $sValue) {
            if (is_numeric($sName)) {
                $iReq++;
                $this->_aParams[ 'req' . $iReq ] = $sValue;
            } else {
                $this->_aParams[ $sName ] = $sValue;
            }
        }
    }

    /**
     * Get all the requests.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_aParams;
    }

    /**
     * Get the domain mame of the site.
     *
     * @return string
     */
    public function getDomain()
    {
        (($sPlugin = Phpfox_Plugin::get('url_getdomain_1')) ? eval($sPlugin) : false);
        if (isset($sPluginReturn)) {
            return $sPluginReturn;
        }

        if ($this->_bUrlRewrite != 3) {
            return $this->_sPath;
        }

        return (($this->_aParams['req1'] == PHPFOX_MODULE_CORE) ? $this->_sPath : preg_replace("/http:\/\/(.*?)\.(.*?)/i", "http://{$this->_aParams['req1']}.$2", $this->_sPath));
    }

    /**
     * Reverse rewrite URLs.
     *
     * @param string $sUrl URL.
     *
     * @return array ARRAY of requests within the STRING URL.
     */
    public function makeReverseUrl($sUrl)
    {
        $aParts = explode('.', $sUrl);
        $aLinks = [];
        foreach ($aParts as $sPart) {
            if (empty($sPart)) {
                continue;
            }

            if (strpos($sPart, '_')) {
                $aLine = explode('_', $sPart);
                $aLine[0] = strtolower(preg_replace('/ +/', '-', preg_replace('/[^0-9a-zA-Z]+/', '', $aLine[0])));
                $aLine[1] = strtolower(preg_replace('/ +/', '-', preg_replace('/[^0-9a-zA-Z]+/', '', $aLine[1])));
                $aLinks[ $aLine[0] ] = $aLine[1];
            } else {
                $sPart = strtolower(preg_replace('/ +/', '-', preg_replace('/[^0-9a-zA-Z]+/', '', $sPart)));
                $aLinks[] = $sPart;
            }
        }

        return $aLinks;
    }

    /**
     * Make an internal link.
     *
     * @param string $sUrl      Internal link.
     * @param array  $aParams   ARRAY of params to include in the link.
     * @param bool   $bFullPath Not using this argument any longer.
     *
     * @return string Full URL.
     */
    public function makeUrl($sUrl, $aParams = [], $bFullPath = false)
    {
        $sUrl = trim($sUrl, '/');
        if (defined('PHPFOX_INSTALLER')) {
            if (is_array($aParams)) {
                $aParams['sessionid'] = Phpfox_Installer::getSessionId();
            } else {
                $aParams = [$aParams, 'sessionid' => Phpfox_Installer::getSessionId()];
            }
        }

        if (preg_match('/https?:\/\//i', $sUrl)) {
            return $sUrl;
        }


        if ($sUrl == 'current') {
            $sUrl = '';
            foreach ($this->_aParams as $sKey => $sValue) {
                if (substr($sKey, 0, 3) == 'req') {
                    $sUrl .= urlencode($sValue) . '.';
                } else {
                    $aParams[ $sKey ] = $sValue;
                }
            }

            if (isset($this->_aParams['req1']) && $this->_aParams['req1'] == 'admincp')
            {
                $aGets = $_GET;
            }
            else
            {
                $aGets = [];
                foreach ($_GET as $k => $v) {
                    if(!preg_match('#^([a-zA-Z0-9\.\-\_]+)$#',$k)){
                        continue;
                    }

                    if (is_scalar($v)) {
                        $aGets[ $k ] = urlencode(htmlspecialchars($v));
                    } else if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            $aGets[ $k ][ $k2 ] = is_string($v2) ? urlencode(htmlspecialchars($v2)) : $v2;
                        }
                    }
                }
            }

            unset($aGets['do']);

            if ($aGets) {
                if (!is_array($aParams)) {
                    $aParams = [];
                }
                $aParams = array_merge($aParams, $aGets);
            }

            $sUrl = rtrim($sUrl, '.');
        }

        if($this->_sPluginCheckUrlIsArray){
            eval($this->_sPluginCheckUrlIsArray);
        }

        // Make it an array if its not an array already (Used as shortcut)
        if (!is_array($aParams)) {
            $aParams = [$aParams];
        }

        if (!defined('PHPFOX_INSTALLER')) {
            if ($sUrl == 'profile') {
                if (empty($aParams[0]) && isset($aParams[1]) && $aParams[1] > 0) {
                    $type = Phpfox::getLib('pages.facade')->getPageItemType($aParams['1']);
                    if ($type == 'groups'){
                        $sUrl = 'groups';
                    } else {
                        $sUrl = 'pages';
                    }
                    unset($aParams[0]);
                } else {
                    $sUrl = '';
                    $sUrl .= (isset($aParams[0]) ? $aParams[0] : Phpfox::getUserBy('user_name'));

                    unset($aParams[0]);
                }
            } else {
                if (preg_match("/profile/i", $sUrl) and Phpfox::isUser()) {
                    $aParts = explode('.', $sUrl);
                    if (isset($aParts[0]) && $aParts[0] == 'profile') {
                        unset($aParts[0]);
                        if (isset($aParts[1]) && $aParts[1] == 'my') {
                            unset($aParts[1]);
                        }
                        $sUrl = '';
                        $sUrl .= (isset($aParams[0]) ? $aParams[0] : Phpfox::getUserBy('user_name'));
                        $sUrl .= '.' . implode('.', $aParts);
                    }
                }
            }

            if ($sUrl == 'profile' && $this->_sCoreModuleCore == PHPFOX_MODULE_CORE) {
                $sUrl = '';
            }
        }
        $sUrl = trim($sUrl, '.');
        $sUrls = '';
        switch ($this->_bUrlRewrite) {
            // www.site.com/foo/bar/
            case 1:
            case 2:
                $aParts = explode('.', $sUrl);
                $sUrls .= $this->_sPath;
                $sUrls .= $this->_makeUrl($aParts, $aParams);
                break;
        }

        if (!defined('PHPFOX_INSTALLER') && ($this->_bForceSecurePages || $this->_bForceSecurePages)) {
            if ($this->_bForceSecurePages) {
                $sUrls = str_replace('http://', 'https://', $sUrls);
            } else {
                if (in_array(str_replace('mobile.', '', $sUrl), Phpfox::getService('core')->getSecurePages())) {
                    $sUrls = str_replace('http://', 'https://', $sUrls);
                }
                else {
                    $sUrls = str_replace('https://', 'http://', $sUrls);
                }
            }
        }

        if($this->_sCheckUrlIsArrayReturn){
            eval($this->_sCheckUrlIsArrayReturn);
        }

        return $sUrls;
    }

    /**
     * Parse a URL string.
     *
     * @param string $sUrl URL.
     *
     * @return array ARRAY converted from the URL STRING.
     */
    public function parseUrl($sUrl)
    {
        $aParams = [];
        switch ($this->_bUrlRewrite) {
            case 1:
                $aParts = explode($this->_sPath, $sUrl);
                $aParams = $this->_parseUrl($aParts[1]);
                break;
            case 2:
                $aParts = explode($this->_sPath, $sUrl);
                $aParams = $this->_parseUrl($aParts[1]);
                break;
            case 3:
                preg_match("/^http:\/\/(.*?)\.(.*?)\/(.*?)$/i", $sUrl, $aMatches);
                $sUrl = $aMatches[1] . '/' . str_replace($this->_sCoreFolder, '', '/' . $aMatches[3]);
                $aParams = $this->_parseUrl($sUrl);
                break;
        }

        return $aParams;
    }

    /**
     * Check if the controller we are on has a registration step.
     *
     * @param int $iReq Request step.
     *
     * @return array 1st value for the array is BOOL to see if this is a registration step. The 2nd value is the next
     *               URL.
     */
    public function isRegistration($iReq)
    {
        $bIsRegistration = false;
        $sNextUrl = null;
        if (Phpfox_Request::instance()->get('req' . $iReq) == 'register' && is_array(Phpfox::getParam('user.registration_steps')) && count(Phpfox::getParam('user.registration_steps'))) {
            $bIsRegistration = true;
            $aUrls = Phpfox::getParam('user.registration_steps');
            $iLastKey = 0;
            foreach ($aUrls as $iKey => $sUrl) {
                if (Phpfox_Module::instance()->getFullControllerName() == $sUrl) {
                    $iLastKey = $iKey;
                }
            }
            $aUrls = array_values($aUrls);

            if (isset($aUrls[ ($iLastKey + 1) ])) {
                $sNextUrl = $aUrls[ ($iLastKey + 1) ];
                if (substr($sNextUrl, -6) == '.index') {
                    $sNextUrl = substr_replace($sNextUrl, '', -6);
                }
                $sNextUrl .= '.register';
            }
        }

        return [$bIsRegistration, $sNextUrl];
    }

    /**
     * Permalink for items.
     *
     * @return    string    Returns the full URL of the link.
     */
    public function permalink($sLink, $iId, $sTitle = null, $bRedirect = false, $sMessage = null, $aExtraLinks = [])
    {
        if ($sMessage !== null) {
            Phpfox::addMessage($sMessage);
        }

        $aExtra = [];
        $aExtra[] = $iId;
        if (!empty($sTitle)) {
            $sTitle = Phpfox::getSoftPhrase($sTitle);
            $aExtra[] = $this->cleanTitle($sTitle);
        }

        if (is_array($sLink)) {
            $iCnt = 0;
            foreach ($sLink as $mKey => $mValue) {
                $iCnt++;
                if ($iCnt === 1) {
                    $sActualLink = $mValue;

                    continue;
                }

                if (is_numeric($mKey)) {
                    $aExtra[] = $mValue;
                } else {
                    if ($mKey == 'view') {
                        $mValue = urlencode($mValue);
                    }
                    $aExtra[ $mKey ] = $mValue;
                }
            }
            $sLink = $sActualLink;
        }

        if (is_array($aExtraLinks) && count($aExtraLinks)) {
            $aExtra = array_merge($aExtra, $aExtraLinks);
        }

        $sUrl = Phpfox_Url::instance()->makeUrl($sLink, $aExtra);

        if ($bRedirect === true) {
            $this->_send($sUrl);
        }

        return $sUrl;
    }

    public function current()
    {
        $current = $this->makeUrl('current');

        return $current;
    }

    /**
     * Clean a items title for the sites URL.
     *
     * @param string $sTitle Title we need to parse and clean.
     *
     * @return string New clean title.
     */
    public function cleanTitle($sTitle)
    {
        if (defined('PHPFOX_LANGUAGE_SHORTEN_BYPASS')) {
            $sTitle = preg_replace('/[ ]+/', '-', trim($sTitle));

            return $sTitle;
        }

        $sTitle = Phpfox::getLib('parse.format')->unhtmlspecialchars($sTitle);
        $sTitle = html_entity_decode($sTitle, null, 'UTF-8');

        $sTitle = strtr($sTitle, '`!"$%^&*()-+={}[]<>;:@#~,./?|' . "\r\n\t\\", '                             ' . '    ');
        $sTitle = strtr($sTitle, ['"' => '', "'" => '']);
        $sTitle = preg_replace('/[ ]+/', '-', trim($sTitle));
        $sTitle = strtolower($sTitle);
        if (function_exists('mb_strtolower')) {
            $sTitle = mb_strtolower($sTitle, 'UTF-8');
        } else {
            $sTitle = strtolower($sTitle);
        }

        if (function_exists('mb_substr')) {
            $sTitle = mb_substr($sTitle, 0, Phpfox::getParam('core.crop_seo_url'), 'UTF-8');
        } else {
            $sTitle = substr($sTitle, 0, Phpfox::getParam('core.crop_seo_url'));
        }

        return $sTitle;
    }

    /**
     * Parse a URL string and convert it into an ARRAY.
     *
     * @param string $sUrl URL string.
     *
     * @return array ARRAY of requests.
     */
    private function _parseUrl($sUrl)
    {
        $aParams = [];
        $aSubParams = explode('/', $sUrl);
        $iCnt = 0;
        foreach ($aSubParams as $sSubParam) {
            if (empty($sSubParam)) {
                continue;
            }

            if (substr($sSubParam, 0, 1) == '#') {
                continue;
            }

            $iCnt++;

            if (strpos($sSubParam, '_')) {
                $aPart = explode('_', $sSubParam);
                if (isset($aPart[0])) {
                    if (count($aPart) > 2) {
                        $aParams[ $aPart[0] ] = (substr_replace($sSubParam, '', 0, (strlen($aPart[0]) + 1)));
                    } else {
                        $aParams[ $aPart[0] ] = (isset($aPart[1]) ? $aPart[1] : '');
                    }
                }
            } else {
                $aParams[ 'req' . $iCnt ] = $sSubParam;
            }
        }

        return $aParams;
    }

    /**
     * Send the user to a new location.
     *
     * @param string $sUrl Full URL.
     */
    private function _send($sUrl, $iHeader = null)
    {
        // Clean buffer
        ob_clean();

        if (Phpfox_Request::instance()->get('is_ajax_post')) {
            header('Content-type: application/json');
            echo json_encode(['redirect' => $sUrl]);
            exit;
        }

        $sUri = Phpfox_Url::instance()->getUri();
        if ($sUri == '/_ajax/') {
            echo '<script>window.location.href = \'' . $sUrl . '\';</script>';
            exit;
        }

        if (defined('PHPFOX_IS_AJAX_PAGE') && PHPFOX_IS_AJAX_PAGE) {
            echo 'window.location.href = \'' . $sUrl . '\';';
            exit;
        }

        (($sPlugin = Phpfox_Plugin::get('librayr_url__send_switch')) ? eval($sPlugin) : false);
        if ($iHeader !== null && isset($this->_aHeaders[ $iHeader ])) {
            header($this->_aHeaders[ $iHeader ]);
        }

        // Send the user to the new location
        header('Location: ' . $sUrl);
        exit;
    }

    /**
     * Input value should not prefix/suffix by "/"
     *
     * @param string $sPart
     *
     * @return string
     */
    public function unityUrl($sPart)
    {
        return isset($this->aRewrite[$sPart])? $this->aRewrite[$sPart]:$sPart;
    }

    /**
     * @param string $url
     * @return string
     */
    public function reverseRewriteUrl($url)
    {
        $aParts  =  explode('/', trim($url,'/'));

        if(isset($this->aReverseRewrite[$url])){
            return $this->aReverseRewrite[$url];
        }

        foreach($aParts as $index=> $sPart){
            if($sPart and isset($this->aReverseRewrite[$sPart])){
                $aParts[$index] =  $this->aReverseRewrite[$sPart];
            }
        }

        return implode('/', $aParts);
    }

    /**
     * Build a URL based on the apache rewrite rules.
     *
     * @param array $aParts  ARRAY of all the URL parts.
     * @param array $aParams ARRAY of all the requests.
     *
     * @return string Converted URL.
     */
    private function _makeUrl(&$aParts, &$aParams)
    {
        if (isset($this->aRewrite[ $aParts[0] ]) && !is_array($this->aRewrite[ $aParts[0] ])) {
            $aParts[0] = $this->aRewrite[ $aParts[0] ];
        }

        $sUrls = '';
        foreach ($aParts as $iPartKey => $sPart) {
            if ($this->_bUrlRewrite == 3 && $iPartKey == 0) {
                continue;
            }

            if (empty($sPart)) {
                continue;
            }

            if ($aParts[0] != 'admincp' && isset($this->aRewrite[ $sPart ]) && !is_array($this->aRewrite[ $sPart ])) {
                $sPart = $this->aRewrite[ $sPart ];
            }

            $sUrls .= str_replace('.', '', $sPart) . '/';
        }

        if ($aParams && is_array($aParams)) {
            $iIteration = 0;
            foreach ($aParams as $sKey => $sValue) {
                if (is_null($sValue)) {
                    continue;
                }

                if ($aParts[0] != 'admincp' && is_numeric($sKey) && isset($this->aRewrite[ str_replace('.', '', $sValue) ]) && !is_array($this->aRewrite[ str_replace('.', '', $sValue) ])) {
                    $sValue = $this->aRewrite[ str_replace('.', '', $sValue) ];
                }

                if (is_numeric($sKey)) {

                    $sUrls .= str_replace('.', '', $sValue) . '/';
                    continue;
                }

                $iIteration++;
                if ($iIteration === 1) {
                    $sUrls .= '?';
                }

                if (is_array($sValue)) {
                    foreach ($sValue as $subKey => $subValue) {
                        if (is_array($subValue)) {
                            foreach ($subValue as $sValue) {
                                $sUrls .= $sKey . '[' . $subKey . '][]=' . $sValue . '&';
                            }
                            continue;
                        }
                        $sUrls .= $sKey . '[' . $subKey . ']=' . $subValue . '&';
                    }

                    continue;
                }

                $sUrls .= $sKey . '=' . $sValue . '&';
            }
            $sUrls = rtrim($sUrls, '&');
        }

        $this->_bIsUsed = false;

        $sSubUrl = rtrim($sUrls, '.');
        if (isset($this->aRewrite[ $sSubUrl ]) && !is_array($this->aRewrite[ $sSubUrl ])) {
            $sUrls = $this->aRewrite[ $sSubUrl ] . '/';
        }

        return $sUrls;
    }

    public function getUri()
    {
        if (empty($_SERVER['REQUEST_URI']))
            return '/';

        $uri  =  '/' . ltrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/');

        $sFolder =  str_replace('/index.php','', $this->_sCoreFolder);

        if($sFolder != '/'){
            $uri = substr_replace($uri, '', 0, strlen(rtrim($sFolder, '/')));
        }

        $uri =  '/'.trim(str_replace('//','/', str_replace('index.php', '', $uri)),'/')  .'/';

        return $uri;
    }

    /**
     * Prepare the main requests.
     *
     */
    private function _setParams()
    {
        if (PHPFOX_IS_AJAX) {
            if (isset($_REQUEST['params'])) {
                foreach ($_REQUEST['params'] as $sReq => $sVal) {
                    if (strpos($sVal, '_') !== false) {
                        $aParts = explode('_', $sVal);
                        $this->_aParams[ $aParts[0] ] = $aParts[1];
                    } else if (strpos($sReq, 'req') !== false) {
                        $this->_aParams[ $sReq ] = $sVal;
                    }
                }
            }
        }

        if (!isset($_GET[ PHPFOX_GET_METHOD ])) {
            $_GET[ PHPFOX_GET_METHOD ] = $this->getUri();
        }

        if (!defined('PHPFOX_INSTALLER')) {
            $oModule = Phpfox_Module::instance();
            $sDefaultModule = $this->_sCoreModuleCore;
        }

        $sRequest = $_GET[ PHPFOX_GET_METHOD ];
        $sRequest = trim($sRequest, '/');
        $aRequest = explode("/", $sRequest);

        $iCnt = 0;
        foreach ($aRequest as $sVar) {
            $sVar = trim($sVar);

            if (!empty($sVar)) {
                if ($iCnt == 0 && $sVar == 'mobile') {
                    $sRequest = preg_replace('/mobile\//i', '', $sRequest, 1);
                    break;
                }
            }
        }

        $aRequest = explode("/", $sRequest);

        // Remove params from sRequest
        preg_match('/([a-z0-9]+_[a-z0-9]+)/i', $sRequest, $aParams);

        $sRequest = trim($sRequest, '/');
        if (isset($this->aRewrite[ $sRequest ])) {
            // this is the final url, do not rewrite
        } else if (isset($this->aReverseRewrite[ $sRequest ])) {
            $sRequest = $this->aReverseRewrite[ $sRequest ];

            $aRequest = (explode('/', $sRequest));
            $iCnt++;
            foreach ($aRequest as $sReq) {
                $this->_aParams[ 'req' . $iCnt ] = $sReq;
            }
            $iCnt = 0;

            if (!empty($aParams)) {
                $aRequest = $aRequest + $aParams;
            }
        } else if (isset($aRequest[0]) && isset($this->aReverseRewrite[ $aRequest[0] ])) {
            $sRequest = $this->aReverseRewrite[ $aRequest[0] ];

            $aRequest[0] = $sRequest;
            $iCnt++;
            foreach ($aRequest as $sReq) {
                $this->_aParams[ 'req' . $iCnt ] = $sReq;
            }
            $iCnt = 0;

            if (!empty($aParams)) {
                $aRequest = $aRequest + $aParams;
            }
        }
        $sRequest = trim($sRequest, '/');


        $bRedirected = false;

        if (isset($this->aReverseRewrite[ $sRequest ])) {
            // we already redirected and should stop now.
            $bRedirected = true;
        } else if (isset($this->aRewrite[ $sRequest ])) {

        }


        foreach ($aRequest as $sVar) {
            $sVar = trim($sVar);

            if (!empty($sVar)) {
                if ($iCnt == 0 && $sVar == 'mobile') {
                    continue;
                }

                $iCnt++;

                $bPass = true;
                if ($iCnt == 1 && !preg_match("/^frame_(.*)$/", $sVar)) {
                    $bPass = false;
                }

                if ($bPass && preg_match('/\_/', $sVar)) {
                    $aPart = explode('_', $sVar);
                    if (isset($aPart[0])) {
                        if (count($aPart) > 2) {
                            $this->_aParams[ $aPart[0] ] = (substr_replace($sVar, '', 0, (strlen($aPart[0]) + 1)));
                        } else {
                            $this->_aParams[ $aPart[0] ] = (isset($aPart[1]) ? $aPart[1] : '');
                        }
                    }

                    if ($iCnt > 2) {
                        $sVar = rawurldecode($sVar);
                        $sVar = rawurlencode($sVar);
                        $this->_aParams[ 'force_req' . $iCnt ] = $sVar;
                    }
                } else {
                    // Override our default requests in case the user has created some special URL rewrites
                    /**
                     * @todo We need to look over this routine. Currently it might be eating up a little too
                     * much extra memory, however from recent tests it seems to be working fine.
                     */

                    if (!defined('PHPFOX_INSTALLER') && $iCnt == 1 && $sDefaultModule != PHPFOX_MODULE_CORE &&
                        $bRedirected != true && isset($this->aRewrite['']['module']) && ($sModule = $this->aRewrite['']['module']) == $sDefaultModule && !$oModule->isModule($sVar)
                    ) {
                        $this->_aParams['req1'] = strtolower($sDefaultModule);
                        $this->_aParams['req2'] = $sVar;
                        $iCnt++;
                        continue;
                    }

                    $sVar = rawurldecode($sVar);
                    $sVar = rawurlencode($sVar);
                    $this->_aParams[ 'req' . $iCnt ] = $sVar;
                }
            }
        }

    }

    /**
     * If the site is using https, we will convert external non https to internal https
     *
     * @param string $sUrl
     *
     * @return string
     */
    public function secureUrl($sUrl)
    {
        if (!is_scalar($sUrl)) {
            return $sUrl;
        }
        if(PHPFOX_IS_HTTPS && Phpfox::getParam('core.use_secure_image_display')) {
            if (filter_var($sUrl, FILTER_VALIDATE_URL) === false) {
                //If not URL
                return $sUrl;
            }

            $aQueryUrl = parse_url($sUrl);
            if (isset($aQueryUrl['host']) && $aQueryUrl['host'] == Phpfox::getParam('core.host')) {
                //If is internal url
                return $sUrl;
            }

            return Phpfox::getLib('url')->makeUrl('', [
                'external' => base64_encode($sUrl)
            ]);
        } else {
            return $sUrl;
        }
    }

    /**
     * Checks to see if a URL exists or not within the rewrite rules.
     *
     * @param string $sUrl  URL name.
     * @param string $sName ID name of the URL.
     *
     * @return bool TRUE if URL exists, FALSE if not.
     */
    private function _isUrl(&$sUrl, $sName)
    {
        if (($sUrl == $sName) || (isset($this->aRewrite[ $sName ]) && $this->aRewrite[ $sName ] == $sUrl)) {
            return true;
        }

        return false;
    }
}