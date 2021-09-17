<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_DONT_SAVE_PAGE', true);

/**
 * Class User_Component_Controller_Verify
 *
 * This controller receives the link for verifying a member's email address
 */
class User_Component_Controller_Verify extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
	    //Redirect to home page if logged
	    if (Phpfox::isUser()) {
	        Phpfox::getLib('url')->send('');
        }
		$this->template()->setTitle(_p('email_verification'))->setBreadCrumb(_p('email_verification'))
			->assign(array(
					'iVerifyUserId' => Phpfox::getLib('session')->get('cache_user_id')
				)
			);
		
		$sHash = $this->request()->get('link', '');
		if ($sHash == '') {}
		elseif (Phpfox::getService('user.verify.process')->verify($sHash))
		{
			if ($sPlugin = Phpfox_Plugin::get('user.component_verify_process_redirection'))
			{
				eval($sPlugin);
			}				
			
			$sRedirect = Phpfox::getParam('user.redirect_after_signup');
							
			if (!empty($sRedirect))
			{
				Phpfox::getLib('session')->set('redirect', $sRedirect);
			}			
			
			// send to the log in and say everything is ok
			Phpfox::getLib('session')->set('verified_do_redirect', '1');
			if (Phpfox::getParam('user.approve_users')) {
				$this->url()->send('', null, _p('your_account_is_pending_approval'));
			}
			$this->url()->send('user.login', null, _p('your_email_has_been_verified_please_log_in_with_the_information_you_provided_during_sign_up'));
		}
		else
		{
			//send to the log in and say there was an error
			Phpfox_Error::set(_p('invalid_verification_link'));
			$iTime = Phpfox::getParam('user.verify_email_timeout');
			if ($iTime < 60)
			{
				$sTime = _p('time_minutes', array('time' => $iTime));
			}
			elseif ($iTime < (60 * 60 * 24)) // one day
			{			
				$sTime = ($iTime == 60 ? _p('time_hour', array('time' => round($iTime / 60))) : _p('time_hours', array('time' => round($iTime / 60))));
			}
			else
			{
				$sTime = _p('time_days', array('time' => round($iTime / (60*60*24))));
			}
            Phpfox::getService('user.verify.process')->sendMail(Phpfox::getLib('session')->get('cache_user_id'));
			$this->template()->assign(array('sTime' => $sTime));
		}		
	}
}
