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
 * @version 		$Id: share.class.php 4545 2012-07-20 10:40:35Z Raymond_Benc $
 */
class Feed_Component_Block_Share extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iFeedId = $this->request()->getInt('feed_id');
		$sShareModule = $this->request()->get('sharemodule');
        //Get real feed, in case share a shared items.
        $aParentFeed = Phpfox::getService('feed')->getParentFeedItem($sShareModule, $iFeedId);
        if (isset($aParentFeed['parent_module_id']) && isset($aParentFeed['parent_feed_id'])) {
            $iFeedId = $aParentFeed['parent_feed_id'];
            $sShareModule = $aParentFeed['parent_module_id'];
        }
		$aShareModule = explode('_', $sShareModule);
		$is_app = ((new Core\App())->exists($sShareModule) ? true : false);

		if (!$is_app && !Phpfox::isModule($aShareModule[0]))
		{
			return false;
		}

		$this->template()->assign(array(
				'iFeedId' => $iFeedId,
				'sShareModule' => $sShareModule,
				'is_app' => $is_app
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('comment.component_block_share_clean')) ? eval($sPlugin) : false);
	}		
}