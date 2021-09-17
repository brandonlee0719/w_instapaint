<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_DONT_SAVE_PAGE', true);

/**
 * Class User_Component_Controller_Sms_Send
 */
class User_Component_Controller_Sms_Send extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if (Phpfox::getUserId())
        {
            Phpfox::getLib('session')->remove('sms_verify_email');
            $this->url()->send('');
        }
        $iStep = 1;
        $aVals = $this->request()->get('val');
        $sPhone = !empty($aVals['phone']) ? $aVals['phone'] : '';
        $sEmail = Phpfox::getLib('session')->get('sms_verify_email');
        $bFail = false;


        if (!empty($aVals['phone'])) {
            $iStep = 2;
        }

        if (!empty($aVals['verify_sms_token'])) {
            $iStep = 3;
        }

        if (!empty($aVals['resend_passcode'])) {
            $iStep = 1;
        }

        $oService = Phpfox::getLib('phpfox.verify');

        if ($iStep == 2) {
            if (!empty($aVals['email'])) {
                $sEmail = $aVals['email'];
                Phpfox::getLib('session')->set('sms_verify_email', $sEmail);
            }

            $sPhone = $aVals['phone'];

            $sSendToken = Phpfox::getService('user.verify')->getVerifyHashByEmail($sEmail);

            $sSendToken = substr($sSendToken, 0, 3) . ' ' . substr($sSendToken, 3);

            $sMsg = _p('sms_registration_verification_message', ['token' => $sSendToken]);

            $bResult = $oService
                ->sendSMS($sPhone, $sMsg);

            if (!$bResult) {
                Phpfox_Error::set(_p('invalid_phone_number_or_contact_admin', ['phone' => $sPhone]));
                $iStep = 1;
            }
        }

        if ($iStep == 3) {
            if (empty($aVals['verify_sms_token'])) {
                Phpfox_Error::set(_p('verify_fail_contact_admin_for_more_info'));
                $bFail = true;
            }
            else {
                $sToken = $aVals['verify_sms_token'];
                if (Phpfox::getService('user.verify.process')->verify($sToken)) {
                    if ($sPlugin = Phpfox_Plugin::get('user.component_verify_process_redirection')) {
                        eval($sPlugin);
                    }

                    $sRedirect = Phpfox::getParam('user.redirect_after_signup');

                    if (!empty($sRedirect)) {
                        Phpfox::getLib('session')->set('redirect', $sRedirect);
                    }

                    // send to the log in and say everything is ok
                    Phpfox::getLib('session')->set('verified_do_redirect', '1');
                    if (Phpfox::getParam('user.approve_users')) {
                        $this->url()->send('', null, _p('your_account_is_pending_approval'));
                    }
                    $this->url()->send('user.login', null, _p('your_account_has_been_verified_please_log_in_with_the_information_you_provided_during_sign_up'));
                } else {
                    Phpfox_Error::set(_p('invalid_verification_token'));
                }
            }
        }

        $this->template()
            ->assign([
                'iStep' => $iStep,
                'sPhone' => $sPhone,
                'sEmail' => $sEmail,
                'bFail' => $bFail
            ])
            ->setTitle(_p('account_verification'))
            ->setBreadCrumb(_p('account_verification'))
            ->setHeader('cache', array(
                    'jquery/plugin/intlTelInput.js' => 'static_script',
                )
            );
    }
}
