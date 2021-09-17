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
 * @package  		Module_Share
 * @version 		$Id: frame.class.php 5269 2013-01-30 09:00:11Z Raymond_Benc $
 */
class Share_Component_Block_Frame extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		static $aBookmarks = array();
		if (empty($aBookmarks))
		{
			$aBookmarks = 'all';
		}
		if (!is_array($aBookmarks))
		{
			$aBookmarks = array();
		}
        $sBookmarkType = $this->getParam('type');
        $sBookmarkUrl = $this->getParam('url');
		$sShareModule = $this->request()->get('sharemodule');
        //Get Real Module_id
		if (Phpfox::isApps($sShareModule)){
            $sModule = $sShareModule;
        } else {
            $aModuleData = explode('_', $sShareModule);
            if (isset($aModuleData[0]) && Phpfox::isModule($aModuleData[0])){
                $sModule = $aModuleData[0];
            } else {
                //not a valid shared item
                return false;
            }
        }
		// add condition.
        if (Phpfox::isApps($sModule)  && !Phpfox::hasCallback($sModule, 'canShareItemOnFeed')) {
            //Don't have callback
            return false;
        }

        $iFeedId = $this->request()->getInt('feed_id');
		$this->template()->assign(array(
				'sBookmarkType' => $sBookmarkType,
				'sBookmarkUrl' => $sBookmarkUrl,
				'sBookmarkTitle' => $this->getParam('title'),
				'bShowSocialBookmarks' => count($aBookmarks) > 0,
				'iFeedId' => $iFeedId,
				'sShareModule' => $sShareModule
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
		(($sPlugin = Phpfox_Plugin::get('share.component_block_frame_clean')) ? eval($sPlugin) : false);
	}
}