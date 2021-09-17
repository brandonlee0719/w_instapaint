<?php
namespace Apps\phpFox_Shoutbox\Ajax;

use Phpfox_Ajax;
use Apps\phpFox_Shoutbox\Service\Shoutbox as sb;

/**
 * Class Ajax
 * @author Neil <neil@phpfox.com>
 * @package Apps\phpFox_Shoutbox\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function add()
    {
        $iTimeId = $this->get('time_id');
        $aVals = [
            'parent_module_id' => $this->get('parent_module_id'),
            'parent_item_id'   => $this->get('parent_item_id'),
            'text'             => $this->get('text'),
        ];
        $iShoutboxId = sb::process()
                         ->add($aVals);
        $this->call("$('[data-value=\"new_shoutbox_" . $iTimeId . "\"]').attr('data-value', '" . $iShoutboxId . "');");
        $this->call("$('#new_shoutbox_" . $iTimeId ."').attr('id', 'shoutbox_message_" . $iShoutboxId . "');");
        $this->call("window.loadTime();");
    }
    
    public function delete()
    {
        $iShoutboxId = $this->get('id');
        $aShoutbox = sb::get()
                       ->getShoutbox($iShoutboxId);
        return sb::process()
                 ->delete($aShoutbox);
    }
    
    public function test()
    {
        sb::get()
          ->getShoutboxes();
    }
}