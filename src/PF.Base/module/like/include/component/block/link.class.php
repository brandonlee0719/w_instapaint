<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: link.class.php 7159 2014-02-26 15:44:39Z Fern $
 */
class Like_Component_Block_Link extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$sModule = $sItemTypeId = Phpfox_Module::instance()->getModuleName();
		
		if ($sModule == 'apps' && Phpfox::isModule('pages'))
		{
			$sModule = 'pages';
		}
		if ($sModule == 'core')
		{
			$sModule = $this->getParam('like_type_id');			
			$sModule = explode('_', $sModule);
			$sModule = $sModule[0];
		}
		else if ($sModule == 'profile')
		{
			$sModule = $sItemTypeId = $this->getParam('like_type_id');	
			$sModule = explode('_', $sModule);
			$sModule = $sModule[0];
		}
		else if ($sModule == 'profile' && ($this->getParam('like_type_id') == 'feed_comment' || $this->getParam('like_type_id') == 'feed_mini'))
		{			
		    $sModule = 'feed';
		}
		if (!$this->getParam('aFeed') && ($aVals = $this->request()->getArray('val')) && isset($aVals['is_via_feed']))
		{		    
		    $this->template()->assign(array('aFeed' => array('feed_id' => $aVals['is_via_feed'])));
		}

		if ($iOwnerId = $this->getParam('like_owner_id', null)) {
            if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $iOwnerId))
            {
                return false;
            }
        }
		$this->template()->assign(array(
				'sParentModuleName' => $sModule,
				'aLike' => array(
					'like_type_id' => $this->getParam('like_type_id'),
					'like_item_id' => $this->getParam('like_item_id'),
					'like_is_liked' => $this->getParam('like_is_liked'),
					'like_is_custom' => $this->getParam('like_is_custom')
				)
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('like.component_block_link_clean')) ? eval($sPlugin) : false);
		
		$this->template()->clean('aLike');
	}
}