<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 */
class Mail_Component_Controller_Thread extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		if (!Phpfox::getParam('mail.threaded_mail_conversation'))
		{
			$this->url()->send('mail');
		}

		$aVals = $this->request()->get('val');
        if ($aVals) {
            // check spam
            if (Phpfox::getParam('mail.mail_hash_check')) {
                $bIsSpam = Phpfox::getLib('spam.hash')->setParams([
                    'table'   => 'mail_hash',
                    'total'   => Phpfox::getParam('mail.total_mail_messages_to_check'),
                    'time'    => Phpfox::getParam('mail.total_minutes_to_wait_for_pm'),
                    'content' => $aVals['message']
                ])->isSpam();

                if ($bIsSpam) {
                    return [
                        'run' => '$("#js_mail_error").html(\'<div class="alert alert-danger alert-dismissable fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . _p('the_content_of_this_item_is_identical_to_something_you_have_added_before_please_try_again') . '</div>\');'
                    ];
                }
            }

            if ($iNewId = Phpfox::getService('mail.process')->add($aVals)) {
                list($aCon, $aMessages) = Phpfox::getService('mail')->getThreadedMail($iNewId);
                $aMessages = array_reverse($aMessages);

                Phpfox_Template::instance()->assign(array(
                        'aMail'          => $aMessages[0],
                        'aCon'           => $aCon,
                        'bIsLastMessage' => true
                    )
                )->getTemplate('mail.block.entry');

                $content = ob_get_contents();
                ob_clean();
                return [
                    'append' => [
                        'to'   => '#mail_threaded_new_message',
                        'with' => $content
                    ],
                    'run' => "\$Core.Attachment.resetAttachmentHolder('#{$this->request()->get('holder_id')}');"
                ];
            }
        }

		$iThreadId = $this->request()->getInt('id');
		
		list($aThread, $aMessages) = Phpfox::getService('mail')->getThreadedMail($iThreadId);
		
		if ($aThread === false)
		{
			return Phpfox_Error::display(_p('unable_to_find_a_conversation_history_with_this_user'));
		}		
		
		$aValidation = array(
			'message' => _p('add_reply')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array(
				'sFormName' => 'js_form', 
				'aParams' => $aValidation
			)
		);			
		
		if ($aThread['user_is_archive'])
		{
			$this->request()->set('view', 'trash');
		}
		
		Phpfox::getService('mail')->buildMenu();
		
		Phpfox::getService('mail.process')->threadIsRead($aThread['thread_id']);

		$iUserCnt = 0;
		$sUsers = '';	
		$bCanViewThread = false;
		$bCanReplyThread = false;
		foreach ($aThread['users'] as $aUser)
		{	
			if ($aUser['user_id'] == Phpfox::getUserId())
			{
				$bCanViewThread = true;
			}
			
			if ($aUser['user_id'] == Phpfox::getUserId())
			{
				continue;
			}			
			
			$iUserCnt++;
			
			if ($iUserCnt == (count($aThread['users']) - 1) && (count($aThread['users']) - 1) > 1)
			{
				$sUsers .= ' &amp; ';
			}	
			else
			{
				if ($iUserCnt != '1')
				{
					$sUsers .= ', ';
				}
			}
			$sUsers .= $aUser['full_name'];

			if (Phpfox::getService('user.privacy')->hasAccess('' . $aUser['user_id'] . '', 'mail.send_message')) {
				$bCanReplyThread = true;
			}
		}
		
		if (!$bCanViewThread)
		{			
			return Phpfox_Error::display('Unable to view this thread.');
		}
		else
		{
			$this->template()->setBreadCrumb(_p('mail'), $this->url()->makeUrl('mail'))->setBreadCrumb($sUsers, $this->url()->makeUrl('mail.thread', array('id' => $iThreadId)), true);
		}
		
		$this->template()->setTitle($sUsers)
			->setTitle(_p('mail'))
			->setHeader('cache', array(
					'mail.js' => 'module_mail',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'
				)
			)
            ->setEditor()
			->assign(array(
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(false),				
					'aMessages' => $aMessages,
					'aThread' => $aThread,
					'sCurrentPageCnt' => ($this->request()->getInt('page', 0) + 1),
					'bCanReplyThread' => $bCanReplyThread
				)
			);
		
		$this->setParam('global_moderation', array(
				'name' => 'mail',
				'custom_fields' => '<div><input type="hidden" name="forward_thread_id" value="' . $aThread['thread_id'] . '" id="js_forward_thread_id" /></div>',
				'menu' => array(
					array(
						'phrase' => _p('forward'),
						'action' => 'forward'
					)			
				)
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
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_thread_clean')) ? eval($sPlugin) : false);
	}	
}