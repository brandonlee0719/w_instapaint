<?php

defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Block_Search_Small extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        $aCurrentValues = $this->getParam('current_values', []);
        $sInputType = $this->getParam('input_type', 'multiple');
        if ($sInputType == 'single' && !empty($aCurrentValues)) {
            $aUserIds = array_column($aCurrentValues, 'user_id');
            $this->template()->assign('sUserIds', implode(',', $aUserIds));
        }

        $this->template()->assign([
            'sInputType' => $sInputType,
            'sInputName' => $this->getParam('input_name', 'friends'),
            'aCurrentValues' => $aCurrentValues
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('friend.component_block_search_small_clean')) ? eval($sPlugin) : false);
    }
}
