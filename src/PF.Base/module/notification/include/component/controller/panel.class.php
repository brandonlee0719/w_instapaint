<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Notification_Component_Controller_Panel
 */
class Notification_Component_Controller_Panel extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $aNotifications = Phpfox::getService('notification')->get();
        $sScript = '<script>$("span#js_total_new_notifications").hide();';

        if (Phpfox::getService('notification')->getUnseenTotal()) {
            $sScript .= '$(\'[data-action="notification_mark_all_read"]\').show()';
        } else {
            $sScript .= '$(\'[data-action="notification_mark_all_read"]\').hide()';
        }
        $sScript .= '</script>';

        $this->template()->assign([
            'aNotifications' => $aNotifications,
            'sScript' => $sScript
        ]);

        //hide all blocks
        Phpfox_Module::instance()->resetBlocks();
    }
}