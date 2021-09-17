<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class WidgetController extends Phpfox_Component
{
    public function process()
    {
        $bIsEdit = false;
        $iPageId = $this->request()->getInt('page_id');

        if (($iWidget = $this->request()->getInt('widget_id')) && $aWidget = \Phpfox::getService('groups')->getForEditWidget($iWidget)) {
            $iPageId = $aWidget['page_id'];
            $this->template()->assign('aForms', $aWidget);
            $bIsEdit = true;
        }

        $aPage = \Phpfox::getService('groups')->getPage($iPageId);

        $this->template()->assign([
                'iPageId' => $iPageId,
                'bIsEdit' => $bIsEdit,
                'sPageUrl' => \Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']),
            ]
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_widget_clean')) ? eval($sPlugin) : false);
    }
}
