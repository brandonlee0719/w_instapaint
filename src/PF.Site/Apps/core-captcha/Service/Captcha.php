<?php

namespace Apps\Core_Captcha\Service;

use \Endroid\QrCode\QrCode;
use Phpfox_Service;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Captcha extends Phpfox_Service
{
    /**
     * @var object
     */
    private $_oSession;

    /**
     * @var
     */
    private $_hImg;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_oSession = Phpfox::getService('log.session');
    }

    /**
     * @param null|string $sCode
     *
     * @return bool
     */
    public function checkHash($sCode = null)
    {
        if (Phpfox::getParam('captcha.captcha_type') == 'recaptcha') {
            $url = 'https://www.google.com/recaptcha/api/siteverify';

            $gDataResponse = isset($_REQUEST['g-recaptcha-response']) ? $_REQUEST['g-recaptcha-response'] : '';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'secret' => Phpfox::getParam('captcha.recaptcha_private_key'),
                'response' => $gDataResponse,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ]));

            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) {
                return false;
            }

            $response = json_decode($response, true);

            if (isset($response['success']) && $response['success'] == true) {
                return true;
            }

            return false;

        }

        if (Phpfox::getParam('core.store_only_users_in_session')) {
            $oSession = Phpfox::getLib('session');

            $sSessionHash = $oSession->get('sessionhash');

            $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('log_session'))
                ->where('session_hash = \'' . $this->database()->escape($sSessionHash) . '\'')
                ->execute('getSlaveRow');

            if (isset($aRow['session_hash']) && $this->_getHash(strtolower($sCode),
                    $aRow['session_hash']) == $aRow['captcha_hash']) {
                return true;
            }
        } else {
            if ($this->_getHash(strtolower($sCode),
                    $this->_oSession->getSessionId()) == $this->_oSession->get('captcha_hash')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $sCode
     *
     * @return void
     */
    public function setHash($sCode)
    {
        if (Phpfox::getParam('core.store_only_users_in_session')) {
            $oRequest = Phpfox::getLib('request');
            $oSession = Phpfox::getLib('session');

            $sSessionHash = $oSession->get('sessionhash');
            $bCreate = true;

            if (!empty($sSessionHash)) {
                $bCreate = false;
                $aRow = $this->database()->select('*')
                    ->from(Phpfox::getT('log_session'))
                    ->where('session_hash = \'' . $this->database()->escape($sSessionHash) . '\'')
                    ->execute('getSlaveRow');

                if (isset($aRow['session_hash'])) {
                    $this->database()->update(Phpfox::getT('log_session'),
                        array('captcha_hash' => $this->_getHash($sCode, $sSessionHash)),
                        "session_hash = '" . $sSessionHash . "'");
                } else {
                    $bCreate = true;
                }
            }

            if ($bCreate) {
                $sSessionHash = $oRequest->getSessionHash();
                $this->database()->insert(Phpfox::getT('log_session'), array(
                        'session_hash' => $sSessionHash,
                        'id_hash' => $oRequest->getIdHash(),
                        'captcha_hash' => $this->_getHash($sCode, $sSessionHash),
                        'user_id' => Phpfox::getUserId(),
                        'last_activity' => PHPFOX_TIME,
                        'location' => '',
                        'is_forum' => '0',
                        'forum_id' => 0,
                        'im_hide' => 0,
                        'ip_address' => '',
                        'user_agent' => ''
                    )
                );
                $oSession->set('sessionhash', $sSessionHash);
            }
        } else {
            $iId = $this->_oSession->getSessionId();

            $this->database()->update(Phpfox::getT('log_session'),
                array('captcha_hash' => $this->_getHash($sCode, $iId)), "session_hash = '" . $iId . "'");
        }
    }

    /**
     * @param string $sText
     *
     * @return void
     */
    public function displayCaptcha($sText)
    {
        while (ob_get_level()) {
            ob_get_clean();
        }

        header("X-Content-Encoded-By: phpFox " . Phpfox::getVersion());
        header("Pragma: no-cache");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Type: image/jpeg');

        $sCaptchaType = Phpfox::getParam('captcha.captcha_type');

        if ($sCaptchaType == 'qrcode') {
            $this->generateQRCodeCaptchaImage($sText);
        } else {
            $this->generateDefaultCaptchaImage($sText);
        }
    }

    /**
     * @param string $sText
     *
     * @return void
     */
    public function generateDefaultCaptchaImage($sText)
    {

        ((Phpfox::getParam('captcha.captcha_use_font') && function_exists('imagettftext')) ? $this->_writeFromFont($sText) : $this->_writeFromString($sText));

        imagejpeg($this->_hImg);
        imagedestroy($this->_hImg);
    }

    /**
     * @param string $sText
     *
     * @return void
     */
    private function generateQRCodeCaptchaImage($sText)
    {

        $qrCode = new QrCode();
        $qrCode->setText($sText)
            ->setSize(60)
            ->setPadding(5)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->render();
    }

    /**
     * @param string $sCharacters
     * @param string $sId
     *
     * @return string
     */
    public function generateCode($sCharacters, $sId)
    {
        if (!$sCode = Phpfox::getLib('session')->get($sId)) {
            $sPossible = Phpfox::getParam('captcha.captcha_code');
            $sCode = '';
            $i = 0;
            while ($i < $sCharacters) {
                $sCode .= substr($sPossible, mt_rand(0, strlen($sPossible) - 1), 1);
                $i++;
            }
            Phpfox::getLib('session')->set($sId, $sCode);
        }
        return strtolower($sCode);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('captcha.service_captcha__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * @param string $sCode
     * @param string $sSalt
     *
     * @return string
     */
    private function _getHash($sCode, $sSalt)
    {
        return md5(md5($sCode) . $sSalt);
    }

    /**
     * @param string $sText
     *
     * @return null
     */
    private function _writeFromFont($sText)
    {
        $iString = strlen($sText);
        $iWidth = (($iString + 5) * 10 * 2);
        $iHeight = 65;
        $iTextSize = 30;
        $sFont = Phpfox::getParam('core.dir_static') . 'image/font/' . Phpfox::getParam('captcha.captcha_font');

        if (!file_exists($sFont)) {
            return $this->_writeFromString($sText);
        }

        $this->_imageCreate($iWidth, $iHeight);

        imagecolorallocate($this->_hImg, 255, 255, 255);
        $nTxtColor = imagecolorallocate($this->_hImg, 0, 0, 0);

        if (!($aBox = @imagettfbbox($iTextSize, 0, $sFont, $sText))) {
            return $this->_writeFromString($sText);
        }

        //Find out the width and height of the text box
        $iTextW = $aBox[2] - $aBox[0];
//        $iTextH = $aBox[5] - $aBox[3];

        if (function_exists('imagefilledellipse')) {
            $nNoiseColor = imagecolorallocate($this->_hImg, 207, 181, 181);
            for ($i = 0; $i < ($iWidth * $iHeight) / 3; $i++) {
                imagefilledellipse($this->_hImg, mt_rand(0, $iWidth), mt_rand(0, $iHeight), 1, 1, $nNoiseColor);
            }
        }

        $iImageLineColor = imagecolorallocate($this->_hImg, 207, 181, 181);
        for ($i = 0; $i < ($iWidth * $iHeight) / 150; $i++) {
            imageline($this->_hImg, mt_rand(0, $iWidth), mt_rand(0, $iHeight), mt_rand(0, $iWidth),
                mt_rand(0, $iHeight), $iImageLineColor);
        }

        // Calculate the positions
        $positionLeft = (($iWidth - $iTextW) / 2) - (20 + $iString);

        for ($i = 0; $i < $iString; $i++) {
            if (!@imagettftext($this->_hImg, $iTextSize, 0, $positionLeft, 30, $nTxtColor, $sFont, $sText[$i])) {
                return $this->_writeFromString($sText);
            }

            $positionLeft += 20;
        }
        return null;
    }

    /**
     * @param string $sText
     *
     * @return null
     */
    private function _writeFromString($sText)
    {
        $iString = strlen($sText);
        $iWidth = (($iString + 5) * 6.4 * 2);
        $iHeight = 40;

        $this->_imageCreate($iWidth, $iHeight);

        imagecolorallocate($this->_hImg, 255, 255, 255);
        $nTxtColor = imagecolorallocate($this->_hImg, 0, 0, 0);

        $positionLeft = 20;

        for ($i = 0; $i < $iString; $i++) {
            imagestring($this->_hImg, rand(3, 8), $positionLeft, rand(8, 23), $sText[$i], $nTxtColor);
            $positionLeft += rand(7, 20);
        }
        return null;
    }

    /**
     * @param int $iWidth
     * @param int $iHeight
     */
    private function _imageCreate($iWidth, $iHeight)
    {
        $this->_hImg = imagecreate($iWidth, $iHeight);
    }
}
