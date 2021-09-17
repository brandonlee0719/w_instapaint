<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Block_Static
 */
class Custom_Component_Block_Static extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        $iId = $this->getParam('field-id');
        //TODO might this block not use anymore
        $aField = Phpfox::getService('custom')->getStaticCustomField($iId);

        $this->template()->assign(array(
            'aField' => $aField
        ));


    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_block_block_clean')) ? eval($sPlugin) : false);
    }
}
