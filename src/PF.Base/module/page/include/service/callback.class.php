<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Callbacks
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Page
 * @version 		$Id: callback.class.php 1614 2010-06-01 10:01:18Z Raymond_Benc $
 */
class Page_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('page');
	}
	
	public function addTrack($iId, $iUserId = null)
	{
	    if (!isset($iUserId)){
	        $iUserId = Phpfox::getUserBy('user_id');
        }
		$this->database()->insert(Phpfox::getT('track'), [
		    'type_id' => 'page',
            'item_id' => (int) $iId,
            'ip_address' => '',
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        ]);
	}	
	
	public function getTagLink()
	{
		return Phpfox_Url::instance()->makeUrl('page.tag');
	}	
	
	public function getTagType()
	{
		return 'page';
	}

	public function massAdmincpProductDelete($sProduct)
	{
		$aPages = $this->database()->select('page_id')
			->from($this->_sTable)
			->where("product_id = '" . $this->database()->escape($sProduct) . "'")
			->execute('getSlaveRows');
		
		foreach ($aPages as $aPage) {
			$this->database()->delete($this->_sTable, 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('page_text'), 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('page_log'), 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('track'), 'item_id = ' . $aPage['page_id'] . ' AND type_id="page"');
		}
	}
	
	public function massAdmincpModuleDelete($sModule)
	{
		$aPages = $this->database()->select('page_id')
			->from($this->_sTable)
			->where("module_id = '" . $this->database()->escape($sModule) . "'")
			->execute('getSlaveRows');
		
		foreach ($aPages as $aPage) {
			$this->database()->delete($this->_sTable, 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('page_text'), 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('page_log'), 'page_id = ' . $aPage['page_id'] . '');
			$this->database()->delete(Phpfox::getT('track'), 'item_id = ' . $aPage['page_id'] . ' AND type_id="page"');
		}
	}

	public function getAttachmentField()
	{
		return array('page', 'page_id');
	}	
	
	public function exportModule($sProduct, $sModule)
	{
		return Phpfox::getService('page')->export($sProduct, $sModule);
	}
	
	public function updateCounterList()
	{
		$aList = array();				
		
		$aList[] =	array(
			'name' => _p('update_tags_pages'),
			'id' => 'page-tag-update'
		);			

		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('tag'))
			->where('category_id = \'page\'')
			->execute('getSlaveField');			
				
		$aRows = $this->database()->select('m.tag_id, oc.page_id AS tag_item_id')
			->from(Phpfox::getT('tag'), 'm')
			->where('m.category_id = \'page_id\'')
			->leftJoin(Phpfox::getT('page'), 'oc', 'oc.page_id = m.item_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');			
			
		foreach ($aRows as $aRow)
		{
			if (empty($aRow['tag_item_id']))
			{
				$this->database()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
			}
		}
		
		return $iCnt;	
	}

	public function getSqlTitleField()
	{
		return array(
			'table' => 'page',
			'field' => 'title'
		);
	}		
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('page.service_callback___call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}