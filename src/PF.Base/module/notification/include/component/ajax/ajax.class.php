<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 100 2009-01-26 15:15:26Z Raymond_Benc $
 */
class Notification_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function update()
	{
		Phpfox::massCallback('getGlobalNotifications');
        
        if ($sPlugin = Phpfox_Plugin::get('notification.component_ajax_update_1')){eval($sPlugin);}

        if (Phpfox::isUser() && Phpfox::getParam('notification.notify_on_new_request')) {
            $this->call('if (typeof $Core.notification !== \'undefined\') $Core.notification.setTitle();');
        }
	}
	
	public function updateSeen()
	{
		Phpfox::isUser(true);
		$sIds = $this->get('id');
		if (!empty($sIds) && Phpfox::getLib('parse.format')->isSerialized($sIds))
		{
			foreach (unserialize($sIds) as $iId)
			{
				Phpfox::getService('notification.process')->updateSeen($iId);
			}		
		}
	}

	public function delete()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getService('notification.process')->deleteById($this->get('id')))
		{
			$this->slideUp('#js_notification_' . $this->get('id'));
		}
	}
	
	public function removeAll()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getService('notification.process')->deleteAll())
		{
            $this->hide('#notification-panel-body .panel_rows');
            $this->append('#notification-panel-body', '<div class="message">'. _p('no_new_notifications') .'</div>');
            $this->html('#notification-panel-body', '');

            $this->hide('#notification-panel-body-xs .panel_rows');
            $this->append('#notification-panel-body-xs', '<div class="message">'. _p('no_new_notifications') .'</div>');
            $this->html('#notification-panel-body-xs', '');

            $this->hide('#notification-panel-body-sm .panel_rows');
            $this->append('#notification-panel-body-sm', '<div class="message">'. _p('no_new_notifications') .'</div>');
            $this->html('#notification-panel-body-sm', '');
		}
		
		$this->hide('.table_clear_ajax');
		$this->call("\$('.js_notification_trash > i').removeClass('fa-circle-o-notch').removeClass('fa-spin').addClass('fa-trash');");
		if ($this->get('redirect')) {
		    $this->call('window.location.href="' . Phpfox::getLib('url')->makeUrl('notification') . '"');
        }
	}

    public function markAllRead()
    {
        Phpfox::isUser(true);
        Phpfox::getService('notification.process')->markAllRead();

        $this->call('$(\'#notification-panel-body\').find(\'.is_new\').removeClass(\'is_new\');');
        $this->call('$(\'#js_total_new_notifications\').html(\'\');');
        $this->slideAlert('#notification-panel-body', _p('marked_all_as_read_successfully'));
    }

    public function markAsRead()
    {
        Phpfox::isUser(true);
        if ($iNotificationId = $this->get('notification_id')) {
            Phpfox::getService('notification.process')->markAsRead($iNotificationId);
        }
    }
}