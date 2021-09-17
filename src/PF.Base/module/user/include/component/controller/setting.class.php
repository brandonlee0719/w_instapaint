<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Setting
 */
class User_Component_Controller_Setting extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		
		Phpfox::isUser(true);
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		$aVals = $this->request()->getArray('val');
		
		if (!isset($aUser['user_id']))
		{
			return Phpfox_Error::display(_p('unable_to_edit_this_account'));
		}	

		if (Phpfox::getUserParam('user.can_change_email'))
		{
			$aValidation['email'] = array(
				'def' => 'email',
				'title' => _p('provide_a_valid_email_address')
			);
		}

		if (Phpfox::getUserParam('user.can_change_own_full_name') && !Phpfox::getParam('user.split_full_name'))
		{
			$aValidation['full_name'] = _p('provide_your_full_name');
		}
		if (Phpfox::getUserParam('user.can_change_own_user_name') && !Phpfox::getParam('user.profile_use_id'))
		{
			$aValidation['user_name'] = array('def' => 'username', 'title' => _p('provide_a_valid_user_name', array(
                'min' => Phpfox::getParam('user.min_length_for_username'),
                'max' => Phpfox::getParam('user.max_length_for_username')
            )));
		}
		
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_setting_process_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if (count($aVals))
		{			
			(($sPlugin = Phpfox_Plugin::get('user.component_controller_setting_process_check')) ? eval($sPlugin) : false);

			if ($oValid->isValid($aVals))
			{
				$bAllowed = true;
				$sMessage = _p('account_settings_updated');
				
				if (Phpfox::getUserParam('user.can_change_email') && $aUser['email'] != $aVals['email'])
				{					
					$bAllowed = Phpfox::getService('user.verify.process')->changeEmail($aUser, $aVals['email']);
					if (is_string($bAllowed))
					{						
						Phpfox_Error::set($bAllowed);
						$bAllowed = false;
					}
					
					if (Phpfox::getParam('user.verify_email_at_signup'))
					{
						$sMessage = _p('account_settings_updated_your_new_mail_address_requires_verification_and_an_email_has_been_sent_until_then_your_email_remains_the_same');
						if (Phpfox::getParam('user.logout_after_change_email_if_verify'))
						{
							$this->url()->send('user.verify', null, _p('email_updated_you_need_to_verify_your_new_email_address_before_logging_in'));
						}
					}					
				}
				
				if ($bAllowed && ($iId = Phpfox::getService('user.process')->update(Phpfox::getUserId(), $aVals, array(
								'changes_allowed' => Phpfox::getUserParam('user.total_times_can_change_user_name'),
								'total_user_change' => $aUser['total_user_change'],
								'full_name_changes_allowed' => Phpfox::getUserParam('user.total_times_can_change_own_full_name'),
								'total_full_name_change' => $aUser['total_full_name_change'],
								'current_full_name' => $aUser['full_name']
							), true
						)
					)
				)
				{
					$this->url()->send('user.setting', null, $sMessage);
				}				
			}
		}		
		
		if (!empty($aUser['birthday']))
		{
			$aUser = array_merge($aUser, Phpfox::getService('user')->getAgeArray($aUser['birthday']));
		}		
		
		$aGateways = Phpfox::getService('api.gateway')->getActive();
        if (!empty($aGateways))
		{
            $aGatewayValues = Phpfox::getService('api.gateway')->getUserGateways($aUser['user_id']);
            foreach ($aGateways as $iKey => $aGateway)
            {
                foreach ($aGateway['custom'] as $iCustomKey => $aCustom)
                {
                    if (isset($aGatewayValues[$aGateway['gateway_id']]['gateway'][$iCustomKey]))
                    {
                        $aGateways[$iKey]['custom'][$iCustomKey]['user_value'] = $aGatewayValues[$aGateway['gateway_id']]['gateway'][$iCustomKey];
                    }
                }
            }	
        }
        
		$aTimeZones = Phpfox::getService('core')->getTimeZones();
		if (count($aTimeZones) > 100) // we are using the php 5.3 way
		{
			$this->template()->setHeader('cache', array('setting.js' => 'module_user'));
		}
		$sFullNamePhrase = Phpfox::getUserParam('user.custom_name_field');
        if (Core\Lib::phrase()->isPhrase($sFullNamePhrase)){
            $sFullNamePhrase = _p($sFullNamePhrase);
        } else {
            $sFullNamePhrase = _p('full_name');
        }
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_setting_settitle')) ? eval($sPlugin) : false);
			
		if (Phpfox::getParam('user.split_full_name') && empty($aUser['first_name']) && empty($aUser['last_name']))
		{
			preg_match('/(.*) (.*)/', $aUser['full_name'], $aNameMatches);
			if (isset($aNameMatches[1]) && isset($aNameMatches[2]))
			{
				$aUser['first_name'] = $aNameMatches[1];
				$aUser['last_name'] = $aNameMatches[2];
			}
			else
			{
				$aUser['first_name'] = $aUser['full_name'];
			}
		}
		
		$this->template()->setTitle(_p('account_settings'))
			->setBreadCrumb(_p('account_settings'))
			->setFullSite()
			->setHeader('cache', array(
					'country.js' => 'module_core',
					'<script type="text/javascript">sSetTimeZone = "' . Phpfox::getUserBy('time_zone') . '";</script>'
				)
			)
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'aForms' => $aUser,
				'bEnable2StepVerification'=>Phpfox::getParam('user.enable_2step_verification'),
				'aTimeZones' => $aTimeZones,
				'sFullNamePhrase' => $sFullNamePhrase,
				'iTotalChangesAllowed' => Phpfox::getUserParam('user.total_times_can_change_user_name'),
				'iTotalFullNameChangesAllowed' => Phpfox::getUserParam('user.total_times_can_change_own_full_name'),
				'aLanguages' => Phpfox::getService('language')->get(array('l.user_select = 1')),
				'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
				'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
				'aCurrencies' => Phpfox::getService('core.currency')->get(),
				'aGateways' => $aGateways				
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_setting_clean')) ? eval($sPlugin) : false);
	}
}
