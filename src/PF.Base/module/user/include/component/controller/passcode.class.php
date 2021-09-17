<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_DONT_SAVE_PAGE', true);

/**
 * Class User_Component_Controller_Passcode
 *
 * This controller receives the link for verifying a member's email address
 */
class User_Component_Controller_Passcode extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if (Phpfox::isUser()) {
            Phpfox_Url::instance()->send('/');
        }
        $this->template()
            ->setTitle(_p('how_to_get_passcode'))
            ->setBreadCrumb(_p('how_to_get_passcode'));

        $sQRCodeUrl = '';
        $oService = Phpfox::getService('user.googleauth');
        if (isset($_REQUEST['val'])){
            $aVals = $_REQUEST['val'];
        } else {
            $aVals = [];
        }
        $sEmail = '';

        if(Phpfox::isUser()){
            $aUser=  Phpfox::getService('user')->get(Phpfox::getUserId(),true);
            $sEmail =  $aUser['email'];
        }

        if (!empty($aVals['email'])) {
            $sEmail = $aVals['email'];
        }

        if(!empty($sEmail)){
            $oService->setUser($sEmail);

            $sTargetUrl = $oService->createUrl($sEmail);

            $sQRCodeUrl = 'https://chart.googleapis.com/chart?' . http_build_query([
                    'cht' => 'qr',
                    'chl' => $sTargetUrl,
                    'chs' => '200x200',
                    'choe' => 'UTF-8',
                ]);
        }

        $this->template()
            ->assign([
                'sQRCodeUrl' => $sQRCodeUrl,
                'sEmail' => $sEmail,
            ]);

    }
}