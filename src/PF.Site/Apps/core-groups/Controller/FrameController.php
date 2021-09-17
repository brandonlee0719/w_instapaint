<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class FrameController extends Phpfox_Component
{
    public function process()
    {
        if (($aVals = $this->request()->getArray('val'))) {
            if (($this->request()->get('widget_id') ? Phpfox::getService('groups.process')->updateWidget($this->request()->get('widget_id'),
                $this->request()->get('val')) : Phpfox::getService('groups.process')->addWidget($this->request()->get('val')))) {
                $aVals = $this->request()->get('val');
                echo '<script type="text/javascript">window.parent.location.href = \'' . Phpfox_Url::instance()->makeUrl('groups.add.widget',
                        ['id' => $aVals['page_id']]) . '\';</script>';
            } else {
                echo '<script type="text/javascript">';
                echo 'window.parent.$(\'#js_groups_widget_error\').html(\'<div class="alert alert-danger">' . implode('<br>',
                        Phpfox_Error::get()) . '</div>\');window.parent.$Core.Groups.resetSubmit();';
                echo '</script>';
            }
            exit;
        } else {
            $this->url()->send('groups');
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_frame_clean')) ? eval($sPlugin) : false);
    }
}
