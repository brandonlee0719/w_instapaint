<?php

namespace Apps\Core_Pages\Controller;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class WidgetController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        $iPageId = $this->request()->getInt('page_id');

        if (($iWidget = $this->request()->getInt('widget_id')) && $aWidget = Phpfox::getService('pages')->getForEditWidget($iWidget)) {
            $iPageId = $aWidget['page_id'];
            $aWidget['widget_text'] = $aWidget['text'];
            $this->template()->assign('aForms', $aWidget);
            $bIsEdit = true;
        }

        $aPage = Phpfox::getService('pages')->getPage($iPageId);

        $this->template()->assign(array(
                'iPageId' => $iPageId,
                'bIsEdit' => $bIsEdit,
                'sPageUrl' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url'])
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_controller_widget_clean')) ? eval($sPlugin) : false);
    }
}
