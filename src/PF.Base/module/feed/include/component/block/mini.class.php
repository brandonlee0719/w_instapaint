<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: mini.class.php 4545 2012-07-20 10:40:35Z Raymond_Benc $
 */
class Feed_Component_Block_Mini extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iParentFeedId = (int) $this->getParam('parent_feed_id');
		$sParentModuleId = $this->getParam('parent_module_id');
		if (!$iParentFeedId)
		{
			return false;
		}

        //Get Real Module_id
        if (Phpfox::isModule($sParentModuleId) || Phpfox::isApps($sParentModuleId)){
            $sModule = $sParentModuleId;
        } else {
            $aModuleData = explode('_', $sParentModuleId);
            if (isset($aModuleData[0]) && Phpfox::isModule($aModuleData[0])){
                $sModule = $aModuleData[0];
            } else {
                return false;
            }
        }

		if (!Phpfox::hasCallback($sModule, 'canShareItemOnFeed'))
		{
			return false;
		}
        $aParentFeedItem = Phpfox::getService('feed')->getParentFeedItem($sParentModuleId, $iParentFeedId);
        if (empty($aParentFeedItem)){
            $aParentFeedItem = [
                'feed_id' => $iParentFeedId,
                'item_id' => $iParentFeedId
            ];
        }

		$aParentFeed = Phpfox::callback($sParentModuleId . '.getActivityFeed', $aParentFeedItem , null, true);
		if ($aParentFeed && !isset($aParentFeed['type_id']))
		{
			$aParentFeed['type_id'] = $sParentModuleId;
		}
		$this->template()->assign(array(
				'aParentFeed' => $aParentFeed
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feed.component_block_mini_clean')) ? eval($sPlugin) : false);
	}	
}