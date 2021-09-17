<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Share_Component_Block_Addthis extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
	    if (!Phpfox::getParam('core.show_addthis_section')) {
	        return false;
        }
	    $sPubId = $this->getParam('pubid', setting('core.addthis_pub_id', ''));
	    $sCustomize = ($sPubId != '') ? $this->getParam('customize', setting('core.addthis_share_button', '')) : '';
	    $sAddthisUrl = $this->getParam('url', '');
	    $sAddthisTitle = $this->getParam('title', '');
	    $sAddthisDesc = $this->getParam('description', '');
	    $sAddthisMedia = $this->getParam('media', '');
        $this->template()->assign([
            'sAddThisPubId' => $sPubId,
            'sAddThisShareButton' => $sCustomize,
            'sAddthisUrl' => $sAddthisUrl,
            'sAddthisTitle' => $sAddthisTitle,
            'sAddthisDesc' => $sAddthisDesc,
            'sAddthisMedia' => $sAddthisMedia
        ]);
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('share.component_block_addthis_clean')) ? eval($sPlugin) : false);
	}
}