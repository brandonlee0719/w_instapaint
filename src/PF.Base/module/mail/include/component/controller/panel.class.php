<?php
defined('PHPFOX') or exit('NO DICE!');

class Mail_Component_Controller_Panel extends Phpfox_Component {
	public function process() {
		Phpfox::isUser(true);

		$iPageSize = 30;
		$this->search()->set(array(
            'type' => 'mail',
            'field' => 'mail.mail_id',
            'search_tool' => array(
                'table_alias' => 'm',
                'search' => array(
                    'action' => $this->url()->makeUrl('mail', array('view' => $this->request()->get('view'), 'id' => $this->request()->get('id'))),
                    'default_value' => _p('search_messages'),
                    'name' => 'search',
                    'field' => array('m.subject', 'm.preview')
                ),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('latest')),
                    'most-viewed' => array('m.viewer_is_new', _p('unread_first'))
                ),
                'show' => array(30)
            ))
		);
		$this->search()->setCondition('AND m.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.is_archive = 0');

		list(, $aMessages,) = Phpfox::getService('mail')->get($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iPageSize);

		$sScript = '';
        $iNumberMessage = (Phpfox::isUser() && Phpfox::isModule('mail')) ? Phpfox::getService('mail')->getUnseenTotal() : -1;

        if ($iNumberMessage) {
            $sScript .= '$("#js_total_unread_messages").html("(' . _p('total_unread', ['total' => ($iNumberMessage > 99 ? '99+' : $iNumberMessage)]) . ')").show();';
            $sScript .= '$(\'[data-action="mail_mark_all_read"]\').show();';
            $sScript .= '$("#hd-message .dropdown-panel-footer").removeClass("one-el");';

        } else {
            $sScript .= '$("#js_total_unread_messages").html("").hide();';
            $sScript .= '$(\'[data-action="mail_mark_all_read"]\').hide();';
            $sScript .= '$("#hd-message .dropdown-panel-footer").addClass("one-el");';
        }


        $sScript = '<script>$Behavior.resetUnreadThreadsCount = function() {'. $sScript . '};</script>';

		$this->template()->assign(array(
				'aMessages' => $aMessages,
                'sScript' => $sScript
			)
		);

        //hide all blocks
        Phpfox_Module::instance()->resetBlocks();
	}
}