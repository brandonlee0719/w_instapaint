<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Password_Verify
 */
class User_Component_Controller_Password_Verify extends Phpfox_Component 
{
	/**
	 * Process the controller
	 *
	 */
	public function process()
	{
		if (Phpfox::isUser())
		{
			$this->url()->send('');
		}		
		
		if ($sRequest = $this->request()->get('id'))
		{
			if ( ($aVals = $this->request()->getArray('val')))
			{
				if (!isset($aVals['newpassword']) || !isset($aVals['newpassword2']) || $aVals['newpassword'] != $aVals['newpassword2'])
				{
					Phpfox_Error::set(_p('your_confirmed_password_does_not_match_your_new_password'));
				}
				else
				{
					if (Phpfox::getService('user.password')->updatePassword($sRequest, $aVals))
					{
						$this->url()->send('user.login', null, _p('password_successfully_updated'));
					}					
				}
			}
            if (Phpfox::getParam('user.shorter_password_reset_routine'))
            {
                if (Phpfox::getService('user.password')->isValidRequest($sRequest) == true)
                {
                    $this->template()->assign(array('sRequest' => $sRequest));
                }
            }
            else
            {
                if (Phpfox::getService('user.password')->verifyRequest($sRequest))
                {
                    $this->url()->send('user.login', null, _p('new_password_successfully_sent_check_your_email_to_use_your_new_password'));
                }
            }
		}
		
		$this->template()->setTitle(_p('password_request_verification'))->setBreadCrumb(_p('password_request_verification'));
	}
}
