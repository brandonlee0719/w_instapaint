<?php

namespace Apps\PHPfox_CDN\Model;

use function foo\func;

/**
 * PHP class that extends the PHPfox CDN core.
 * We use
 *
 * @package Apps\PHPfox_CDN\Model
 */
class CDN extends \Core\CDN
{

    private $_server_id;

    private $_servers = [];

    public function __construct()
    {
        $this->_servers =  get_from_cache('pf_cdn_servers', function (){
            $aResult  = [];
            foreach (storage()->all('pf_cdn_servers') as $iKey => $aServer) {
                $iKey++;
                $iKey++;
                $aResult[$iKey] = (array)$aServer->value;
            }
            return $aResult;
        });

        $this->_server_id = array_rand($this->_servers);
    }

    public function setServerId($server_id)
    {
        $this->_server_id = $server_id;
    }

    public function getUrl($path, $server_id = null)
    {
        if (!setting('pf_cdn_enabled')) {
            return $path;
        }

        $sPath = str_replace(\Phpfox::getParam('core.path_file'), '', $path);
        $sPath = str_replace("\\", '/', $sPath);

        // $aParts = explode('.', $sPath);
        $iServerId = ($server_id === null ? $this->getServerId() : $server_id);
        if (!isset($this->_servers[$iServerId])) {
            return '';
        }

        // d($sPath);

        $url = $this->_servers[$iServerId]['url'] . $sPath;

        return $url;
    }

    public function put($file, $name = null)
    {
        if (empty($name)) {
            $name = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $file));
        }

        $aPost = array(
            'file_name' => $name,
            'upload' => '@' . $file . '',
            'action' => 'upload',
            'cdn_key' => $this->_servers[$this->_server_id]['key']
        );

        $mReturn = $this->_send($aPost);

        $mReturn = (array)$mReturn;
        if (\Phpfox::getParam('core.keep_files_in_server') == false) {
            register_shutdown_function(function () use ($file) {
                @unlink($file);
            });
        }

        if (isset($mReturn['pass']) && !$mReturn['pass']) {
            return false;
        }

        return true;
    }

    public function getServerId()
    {
        return $this->_server_id;
    }

    public function remove($file)
    {
        $file = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $file));

        $this->_send([
            'action' => 'remove',
            'file_name' => $file,
            'cdn_key' => $this->_servers[$this->_server_id]['key']
        ], true);
    }

    public function __returnObject()
    {
        return $this;
    }

    private function _send($aPost, $bIsDelete = false)
    {
        $hCurl = curl_init();

        curl_setopt($hCurl, CURLOPT_URL, rtrim($this->_servers[$this->_server_id]['upload'], '/') . '/phpfox-cdn.php');
        curl_setopt($hCurl, CURLOPT_HEADER, false);
        curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($hCurl, CURLOPT_POST, true);

        // https://github.com/sendgrid/sendgrid-php/issues/38
        if (!$bIsDelete && defined('PHP_VERSION_ID') && PHP_VERSION_ID > 50500) {
            $sMIME = null;
            $sFileName = substr($aPost['upload'], 1);

            if (function_exists('mime_content_type')) {
                $sMIME = mime_content_type($sFileName);
            } else {
                $hFileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $sMIME = finfo_file($hFileInfo, $sFileName);
                finfo_close($hFileInfo);
            }
            $aPost['upload'] = new \CurlFile($sFileName, $sMIME, $sFileName);
        }

        curl_setopt($hCurl, CURLOPT_POSTFIELDS, $aPost);

        $mReturn = curl_exec($hCurl);

        $mReturn = json_decode($mReturn);

        if (is_object($mReturn) && isset($mReturn->output->error)) {
            error($mReturn->output->error);
        }

        curl_close($hCurl);

        return $mReturn;
    }
}
