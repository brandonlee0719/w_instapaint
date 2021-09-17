<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 */
class Mail_Component_Controller_Compose extends Phpfox_Component
{
	private $_bReturn = null;
	
	public function process()
	{			
		Phpfox::isUser(true);		
		Phpfox::getUserParam('mail.can_compose_message', true);		
		$bClaiming = ($this->getParam('page_id') != false);
		
		if (Phpfox::getParam('mail.spam_check_messages') && Phpfox::isSpammer())
		{			
			return Phpfox_Error::display(_p('currently_your_account_is_marked_as_a_spammer'));
		}
		
		$aVals = $this->request()->getArray('val');
		$bIsSending = isset($aVals['sending_message']);
		
		if (($iUserId = $this->request()->get('id')) || ($iUserId = $this->getParam('id')))
		{
			$aUser = Phpfox::getService('user')->getUser($iUserId, Phpfox::getUserField());
			if (isset($aUser['user_id']))
			{
				
				if ($bClaiming == false && $bIsSending != true && Phpfox::getService('mail')->canMessageUser($aUser['user_id']) == false)
				{
					return Phpfox_Error::display(_p('unable_to_send_a_private_message_to_this_user_at_the_moment'));
				}
				
				$this->template()->assign('aUser', $aUser);
				if ($bClaiming)
				{
					$aPage = Phpfox::getService('pages')->getPage($this->getParam('page_id'));
					$this->template()->assign(array(
						'iPageId' => $this->getParam('page_id'),
						'aPage' => $aPage,
						'sMessageClaim' => _p('page_claim_message', array(
							'title' => $aPage['title'],
							'url' => ($aPage['vanity_url'] ? Phpfox_Url::instance()->makeUrl($aPage['vanity_url']) : Phpfox::permalink('pages', $aPage['page_id'], $aPage['title']))
						))));
				}
			}
			
			(($sPlugin = Phpfox_Plugin::get('mail.component_controller_compose_controller_to')) ? eval($sPlugin) : false);
		}
		
		$bIsThreadForward = false;
		if (($iThreadId = $this->request()->getInt('forward_thread_id')))
		{
			$bIsThreadForward = true;
		}
		
		$aValidation = array(
			'subject' => _p('provide_subject_for_your_message')
		);
		// require message when have no attachment
		if (empty($aVals['attachment']) && empty($this->request()->get('has_attachment'))) {
		    $aValidation['message'] = _p('provide_message');
        }
		
		if (Phpfox::getParam('mail.threaded_mail_conversation'))
		{
			unset($aValidation['subject']);
		}
		
		if (Phpfox::isModule('captcha') && Phpfox::getUserParam('mail.enable_captcha_on_mail'))
		{
			$aValidation['image_verification'] = _p('complete_captcha_challenge');
		}		
		
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_compose_controller_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'js_form', 
				'aParams' => $aValidation
			)
		);
		
		if (($aVals = $this->request()->getArray('val')))
		{			
			// Lets make sure they are actually trying to send someone a message.			
			if (((!isset($aVals['to'])) || (isset($aVals['to']) && !count($aVals['to']))) && (!isset($aVals['copy_to_self']) || $aVals['copy_to_self'] != 1))
			{
				Phpfox_Error::set(_p('select_a_member_to_send_a_message_to'));
			}
						
			if ($oValid->isValid($aVals))
			{
				if (Phpfox::getParam('mail.mail_hash_check'))
				{
					Phpfox::getLib('spam.hash')->setParams([
                        'table' => 'mail_hash',
                        'total' => Phpfox::getParam('mail.total_mail_messages_to_check'),
                        'time' => Phpfox::getParam('mail.total_minutes_to_wait_for_pm'),
                        'content' => $aVals['message']
                    ])->isSpam();
				}
				
				if (Phpfox::getParam('mail.spam_check_messages'))
				{
					if (Phpfox::getLib('spam')->check(array(
                        'action' => 'isSpam',
                        'params' => array(
                            'module' => 'comment',
                            'content' => Phpfox::getLib('parse.input')->prepare($aVals['message'])
								)
							)
						)
					)
					{
						Phpfox_Error::set(_p('this_message_feels_like_spam_try_again'));
					}
				}

                if (Phpfox_Error::isPassed()) {
                    if ($bClaiming) {
                        $aVals['claim_page'] = true;
                    }
                    if (($aIds = Phpfox::getService('mail.process')->add($aVals, $bClaiming))) {
                        if (isset($aVals['page_id']) && !empty($aVals['page_id'])) {
                            Phpfox_Database::instance()->insert(Phpfox::getT('pages_claim'), array('status_id' => '1', 'page_id' => ((int)$aVals['page_id']), 'user_id' => Phpfox::getUserId(), 'time_stamp' => PHPFOX_TIME));
                        }

                        if (PHPFOX_IS_AJAX) {
                            $this->_bReturn = true;
                            return true;
                        }

                        if (Phpfox::getParam('mail.threaded_mail_conversation')) {
                            $this->url()->send('mail.thread', array('id' => $aIds));
                        } else {
                            if (count($aIds) > 1) {
                                $this->url()->send('mail.view', array('id' => base64_encode(serialize($aIds))));
                            } elseif (isset($aIds[0])) {
                                $this->url()->send('mail.view', array('id' => $aIds[0]));
                            }
                        }
                    } else {
                        if (PHPFOX_IS_AJAX) {
                            $this->_bReturn = false;
                            return false;
                        }
                    }
                } else {
                    if (PHPFOX_IS_AJAX) {
                        $this->_bReturn = false;
                        return false;
                    }
                }
            } else {
                if (PHPFOX_IS_AJAX) {
                    $this->_bReturn = false;
                    return false;
                }
            }
		}
		
		Phpfox::getService('mail')->buildMenu();
		if (Phpfox::isModule('friend'))
		{
			$this->template()->setPhrase(array('loading'));
		}
		$this->template()->setTitle(_p('compose_new_message'))
			->setBreadCrumb(_p('mail'), $this->url()->makeUrl('mail'))
			->setBreadCrumb(_p('compose_new_message'), $this->url()->makeUrl('mail.compose'), true)
			->setPhrase(array(
                'add_new_folder',
                'adding_new_folder',
                'view_folders',
                'edit_folders',
                'you_will_delete_every_message_in_this_folder',
            ))
			->setEditor()
			->setHeader('cache', array(
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                'mail.js' => 'module_mail',
            ))
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'iMaxRecipients' => Phpfox::getUserParam('mail.send_message_to_max_users_each_time'),
				'bIsThreadForward' => $bIsThreadForward,
				'sThreadsToForward' => $this->request()->get('forwards'),
				'sForwardThreadId' => $iThreadId
			)
		);

		$this->setParam('attachment_share', array(		
				'type' => 'mail',
				'inline' => true,
				'id' => 'js_form_mail'
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
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_compose_clean')) ? eval($sPlugin) : false);
	}
	
	public function getReturn()
	{
		if (!$this->_bReturn)
		{
			Phpfox_Ajax::instance()->call('$Core.processForm(\'#js_mail_compose_submit\', true);');
		}
		
		return $this->_bReturn;
	}
}