<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Login
 */
class User_Component_Controller_Login extends Phpfox_Component 
{	
	/**
	 * Controller
	 */
	public function process()
	{
		define('PHPFOX_DONT_SAVE_PAGE', true);

		if (Phpfox::isUser())
		{
			$this->url()->send('profile');
		}
        if (defined('PHPFOX_IS_AJAX_PAGE')){
            $sMainUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $sMainUrl = rtrim($sMainUrl, '/');
            $sMainUrl = str_replace('index.php', '', $sMainUrl);
            $sIndexUrl = $this->url()->makeUrl('');
            $sIndexUrl = str_replace('index.php', '', $sIndexUrl);
            $sMainUrl = rtrim($sMainUrl, '/');
            $sIndexUrl = rtrim($sIndexUrl, '/');
            if ($sMainUrl == $sIndexUrl){
                $sMainUrl = '';
            } else {
                $sMainUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            }
        } else {
            $sMainUrl = '';
        }

		$bEnable2StepVerification = Phpfox::getParam('user.enable_2step_verification');

		switch (Phpfox::getParam('user.login_type'))
		{
			case 'user_name':
				$aValidation['login'] = _p('provide_your_user_name');
				break;
			case 'email':
				$aValidation['login'] = _p('provide_your_email');
				break;				
			default:
				$aValidation['login'] = _p('provide_your_user_name_email');
		}
		$aValidation['password'] = _p('provide_your_password');

		if (Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_login'))
		{
			$aValidation['image_verification'] = _p('complete_captcha_challenge');
		}
		
		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_login_form', 'aParams' => $aValidation));
		
		if ($aVals = $this->request()->getArray('val'))
		{

			if ($oValid->isValid($aVals))
			{

				list($bLogged, $aUser) = (Phpfox::getService('user.auth')->login($aVals['login'], $aVals['password'], (isset($aVals['remember_me']) ? true : false), Phpfox::getParam('user.login_type')));

				if($bLogged && $bEnable2StepVerification){
					$oGoogleAuthService = Phpfox::getService('user.googleauth');
					if(!$oGoogleAuthService->authenticateUser($aUser['email'], $aVals['passcode'])){
						$bLogged = false;
						Phpfox::getService('user.auth')->logout();
						Phpfox_Error::set(_p('invalid_passcode'));
					}
				}

				if ($bLogged)
				{
					$sReturn = '';
                    if (Phpfox::getParam('core.redirect_guest_on_same_page')) {
                        $sReturn = Phpfox::getLib('session')->get('redirect');
                        if (is_bool($sReturn)) {
                            $sReturn = '';
                        }
                        if (empty($sReturn) && !empty($sMainUrl)){
                            $sReturn = $sMainUrl;
                        }
                        if (!filter_var($sReturn, FILTER_VALIDATE_URL) === false) {

                        } elseif ($sReturn) {
                            $aParts = explode('/', trim($sReturn, '/'));
                            if (isset($aParts[0])) {
                                $aParts[0] = Phpfox_Url::instance()->reverseRewrite($aParts[0]);
                            }
                            if (isset($aParts[0]) && !Phpfox::isModule($aParts[0])) {
                                $aUserCheck = Phpfox::getService('user')->getByUserName($aParts[0]);
                                if (isset($aUserCheck['user_id'])) {
                                    if (isset($aParts[1]) && !Phpfox::isModule($aParts[1])) {
                                        $sReturn = '';
                                    }
                                } else {
                                    $sReturn = '';
                                }
                            }
                        }
                    }


					if ($sReturn == 'profile')
					{
						$sReturn = $aUser['user_name'];
					}

                    if (Phpfox::getParam('user.redirect_after_login')) {
                        $sReturn = Phpfox::getParam('user.redirect_after_login');
                    }

					Phpfox::getLib('session')->remove('redirect');

					if (preg_match('/^(http|https):\/\/(.*)$/i', $sReturn))
					{
						$this->url()->forward($sReturn);
					}

					$sReturn = trim($sReturn, '/');
					$sReturn = str_replace('/', '.', $sReturn);			
					
					Phpfox::getLib('session')->remove('redirect');

					if (isset($aUser['status_id']) && $aUser['status_id'] == 1)
					{
						if(Phpfox::getParam('core.registration_sms_enable')){
							Phpfox::getLib('session')->set('sms_verify_email', $aUser['email']);
							$this->url()->send('user.sms.send', null, _p('you_still_need_to_verify_your_account'));
						}else{
							$this->url()->send($sReturn, null, _p('you_still_need_to_verify_your_email_address'));
						}

					}
					
					if (Phpfox::getParam('user.verify_email_at_signup'))
					{
						$bDoRedirect = Phpfox::getLib('session')->get('verified_do_redirect');
						Phpfox::getLib('session')->remove('verified_do_redirect');
						if ( (int)$bDoRedirect == 1 && Phpfox::getParam('user.redirect_after_signup') != '')
						{
							$sReturn = Phpfox::getParam('user.redirect_after_signup');
						}
					}
					$this->url()->send($sReturn);
				}
				else
				{
					if ($sPlugin = Phpfox_Plugin::get('user.controller_login_login_failed')){eval($sPlugin);}
				}
			}
		}
		
		$sSiteName = Phpfox::getParam('core.site_title');
		$this->template()->setBreadCrumb(_p('sign_in_title'))
			->setTitle(_p('sign_in_title'))
			->assign(array(
				'bEnable2StepVerification'=>$bEnable2StepVerification,
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'sSiteName' => $sSiteName,
				'sSignUpPage' => $this->url()->makeUrl('user.register'),
				'sDefaultEmailInfo' => ($this->request()->get('email') ? trim(base64_decode($this->request()->get('email'))) : '')
			)
		);
	}
}