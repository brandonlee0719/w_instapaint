<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Password_Request
 */
class User_Component_Controller_Password_Request extends Phpfox_Component
{
    /**
     * Process the controller
     *
     */
    public function process()
    {
        if (Phpfox::isUser()) {
            $this->url()->send('');
        }

        $aValidation['email'] = _p('enter_your_email');
        if (Phpfox::isModule('captcha')) {
            $aValidation['image_verification'] = _p('complete_captcha_challenge');
        }
        $oValid = Phpfox_Validator::instance()->set(array(
            'sFormName' => 'js_request_password_form',
            'aParams' => $aValidation
        ));

        if ($aVals = $this->request()->getArray('val')){
            if ($oValid->isValid($aVals)) {
                if (Phpfox::getService('user.password')->requestPassword($aVals)) {
                    $this->url()->send('user.login', null,
                        _p('password_request_successfully_sent_check_your_email_to_verify_your_request'));
                }
            }
        }

        $this->template()->setTitle(_p('password_request'))->setBreadCrumb(_p('password_request'));
    }
}
