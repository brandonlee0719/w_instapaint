<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Tag_Service_Process
 */
class Tag_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('tag');
	}
	
	public function add($sType, $iItemId, $iUserId, $sTags, $bHashTags = false)
	{			
		$oFilter = Phpfox::getLib('parse.input');
		if ($bHashTags)
		{
			$aTags = Phpfox::getLib('parse.output')->getHashTags($sTags);
		}
		else {
			if (is_array($sTags)) {
				$aTags = $sTags;
			} else {
                Phpfox::getService('ban')->checkAutomaticBan($sTags);
				$aTags = explode(',', $sTags);
			}
			//Clean all html
			foreach ($aTags as $tagKey => $sTagValue) {
			    $aTags[$tagKey] = Phpfox::getLib('parse.output')->cleanTag($sTagValue);
            }
		}
		$aCache = array();
		foreach ($aTags as $sTag)
		{
			$sTag = trim($sTag);
			$sTag = mb_convert_case($sTag, MB_CASE_LOWER, "UTF-8");
			
			if (empty($sTag))
			{
				continue;
			}
			
			if (isset($aCache[$sTag]))
			{
				continue;
			}
			
			$this->database()->insert(Phpfox::getT('tag'), array(
					'item_id' => $iItemId,
					'category_id' => $sType,
					'user_id' => $iUserId,
					'tag_text' => $oFilter->clean($sTag, 255),
                    'tag_type' => ($bHashTags) ? 1 : 0,
					'tag_url' => urldecode($sTag),
					'added' => PHPFOX_TIME
				)
			);
			
			$aCache[$sTag] = true;
		}		
	}
	
	/**
	 * 
	 *
	 * @param string $sType
	 * @param int $iItemId
	 * @param string $sTags
	 * @return int|bool
	 */	
	public function update($sType, $iItemId, $iUserId, $sTags = null, $bHashTags = false)
	{
		if ($sTags !== null)
		{
			/* Since tags are unique to each item it should be safe to delete every tag
			 * belonging to one item and add the new ones
			 */
            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . (int)$iItemId . ' AND category_id = \'' . $this->database()->escape($sType) . '\' AND user_id = ' . (int)$iUserId. (($bHashTags) ? ' AND tag_type = 1' : ' AND tag_type = 0'));

			return $this->add($sType, $iItemId, $iUserId, $sTags, $bHashTags);
		}
		else
		{
			// just delete every tag
			$this->database()->delete(Phpfox::getT('tag'), "item_id = " . (int) $iItemId . " AND category_id = '" . $this->database()->escape($sType) . "' AND user_id = " . (int) $iUserId. (($bHashTags) ? ' AND tag_type = 1' : ' AND tag_type = 0'));
		}

        Phpfox::getLib('cache')->removeGroup('tag');
		
		return true;
	}
	
	public function deleteForItem($iUserId, $iItemId, $sCategory)
	{		
		$this->database()->delete($this->_sTable, "item_id = " . $iItemId . " AND category_id = '" . $this->database()->escape($sCategory) . "'");

        Phpfox::getLib('cache')->removeGroup('tag');
		
		return true;
	}
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('tag.service_process__call'))
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
