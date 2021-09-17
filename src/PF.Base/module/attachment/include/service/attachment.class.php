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
 * @package  		Module_Attachment
 * @version 		$Id: attachment.class.php 7033 2014-01-09 19:39:28Z Fern $
 */
class Attachment_Service_Attachment extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('attachment');
		
		(($sPlugin = Phpfox_Plugin::get('attachment.service_attachment___construct')) ? eval($sPlugin) : false);
	}

    /**
     * @param array | string $aConds
     * @param string $sSort
     * @param bool $bCount
     *
     * @return array
     */
    public function get($aConds, $sSort = 'attachment.time_stamp DESC', $bCount = true)
    {
        $iCnt = 0;
        if ($bCount) {
            (($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_get_count')) ? eval($sPlugin) : false);

            $iCnt = $this->database()->select('COUNT(*)')
                ->from($this->_sTable, 'attachment')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = attachment.user_id')
                ->where($aConds)
                ->execute('getSlaveField');
        }

        (($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_select')) ? eval($sPlugin) : false);
        $iPage = Phpfox_Request::instance()->get('page');
        $aItems = $this->database()->select('attachment.*,' . Phpfox::getUserField('u'))
            ->from($this->_sTable, 'attachment')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = attachment.user_id')
            ->where($aConds)
            ->order($sSort)
            ->limit($iPage, 12, $iCnt, false, false)
            ->execute('getSlaveRows');

        foreach ($aItems as $iKey => &$aItem) {
            $aItem['url'] = str_replace('%s', '', $aItem['destination']);

            if ($aItem['is_video']) {
                $aItem['video_image_destination'] = substr_replace($aItem['destination'], '%s.jpg', -4);
            }

            $aItem['inline'] = [
                'name' => $aItem['description'] ? "$('#js_description{$aItem['attachment_id']}').html()" : $aItem['file_name'],
                'path' => $aItem['is_image'] ?
                    Phpfox::getLib('image.helper')->display([
                        'server_id' => $aItem['server_id'],
                        'title' => $aItem['description'],
                        'path' => 'core.url_attachment',
                        'file' => $aItem['destination'],
                        'suffix' => '_view',
                        'max_width' => 'attachment.attachment_max_medium',
                        'max_height' => 'attachment.attachment_max_medium',
                        'return_url' => true
                    ]) : '',
                'url' => $aItem['is_image'] ? Phpfox::getParam('core.url_attachment') . sprintf($aItem['destination'], '') : ''
            ];
        }

        return array($iCnt, $aItems);
    }

    /**
     * @param int $iItemId
     * @param string $sCategory
     * @param null|int $iUserId
     *
     * @param bool $bGetArray
     * @return string
     */
	public function getForItemEdit($iItemId, $sCategory, $iUserId= null, $bGetArray = false)
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_getforitemedit_start')) ? eval($sPlugin) : false);
		
		$aRows = $this->database()->select('attachment_id')
			->from($this->_sTable)
			->where("item_id = " . (int) $iItemId . " AND category_id = '" . $this->database()->escape($sCategory) . "'" . ($iUserId ? (" AND user_id = " . $iUserId) : ''))
			->execute('getSlaveRows');
        
        if (!count($aRows)) {
            return '';
        }
        
        $aItems = [];
        foreach ($aRows as $aRow) {
            $aItems[] = $aRow['attachment_id'];
        }
        
        (($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_getforitemedit_end')) ? eval($sPlugin) : false);
		return $bGetArray ? $aItems : (implode(',', $aItems) . ',');
	}
    
    /**
     * Get an attachment for download
     * @param int $iId
     *
     * @return array
     */
	public function getForDownload($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_getfordownload')) ? eval($sPlugin) : false);
        $sCacheId = $this->cache()->set('attachment_item_' . (int) $iId);

        if (!$aRow = $this->cache()->get($sCacheId)) {
            $aRow = $this->database()
                ->select('attachment.*, attachment_type.mime_type')
                ->from($this->_sTable, 'attachment')
                ->leftJoin(Phpfox::getT('attachment_type'), 'attachment_type', 'attachment_type.extension = attachment.extension')
                ->where('attachment.attachment_id = ' . (int)$iId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aRow);
            Phpfox::getLib('cache')->group('attachment', $sCacheId);
        }
		return $aRow;
	}

    /**
     * Get number attachment on an item
     * @param int $iItemId
     * @param string $sCategory
     *
     * @param bool $bNoCache
     * @return int
     */
	public function getCountForItem($iItemId, $sCategory, $bNoCache = false)
	{
	    $sCacheId = $this->cache()->set('attachment_' . $sCategory . '_count_' . $iItemId);

        if ($bNoCache || !$iItemCount = $this->cache()->get($sCacheId)){
            $iItemCount = $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where("item_id = " . (int) $iItemId . " AND category_id = '" . $this->database()->escape($sCategory) . "'")
                ->execute('getSlaveField');
            
            $this->cache()->save($sCacheId, $iItemCount);
            Phpfox::getLib('cache')->group('attachment', $sCacheId);
        }

	    return $iItemCount;
	}
    
    /**
     * @param string $sIds
     *
     * @return array
     */
	public function verify($sIds)
	{
        if (empty($sIds)) {
            return [];
        }
        
        $sCacheId = $this->cache()->set('attachment_verify_' . md5($sIds));
        if (!$aIds = $this->cache()->get($sCacheId)) {
            $aRows = $this->database()->select('attachment.attachment_id, attachment.destination, attachment.extension, attachment.server_id, attachment.is_inline, attachment.is_image, attachment.is_video')
                ->from($this->_sTable, 'attachment')
                ->where('attachment.attachment_id IN(' . $sIds . ')')
                ->execute('getSlaveRows');
    
            $aIds = [];
            foreach ($aRows as $aRow) {
                $aRow['video_image_destination'] = substr_replace($aRow['destination'], '%s.jpg', -4);
                $aIds[$aRow['attachment_id']] = $aRow;
            }
            $this->cache()->save($sCacheId, $aIds);
            Phpfox::getLib('cache')->group('attachment', $sCacheId);
        }
        return $aIds;
	}
    
    /**
     * Get total attachment of a user
     * @param null|int $iUserId
     *
     * @return int
     */
	public function getTotal($iUserId = null)
	{
	    $iUserId = ($iUserId === null ? Phpfox::getUserId() : (int) $iUserId);
        $sCacheId = $this->cache()->set('attachment_total_' . $iUserId);

        if (!$iTotal = $this->cache()->get($sCacheId)){
            $iTotal = $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('user_id = ' . $iUserId)
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotal);
            Phpfox::getLib('cache')->group('attachment', $sCacheId);
        }
	   return $iTotal;
	}
    
    /**
     * Check a user can upload more attachment
     * @param null|int $iUserId
     *
     * @return bool
     */
	public function isAllowed($iUserId = null)
	{
	    $iLimit = Phpfox::getUserParam('attachment.attachment_limit');
        if ($iLimit == 'null'){
            return true;
        }
        if ($this->getTotal($iUserId) < $iLimit) {
            return true;
        }
        return false;
	}
    
    /**
     * Check a user allow to view an attachment
     * @param int $iId
     * @param string $sUserPerm
     * @param string $sGlobalPerm
     *
     * @return bool
     */
	public function hasAccess($iId, $sUserPerm, $sGlobalPerm)
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_hasaccess_start')) ? eval($sPlugin) : false);
		$sRowCacheId = $this->cache()->set('attachment_row_' . (int) $iId);

        if (!$aRow = $this->cache()->get($sRowCacheId)) {
            $aRow = $this->database()->select('u.user_id')
                ->from($this->_sTable, 'a')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')
                ->where('a.attachment_id = ' . (int) $iId)
                ->execute('getSlaveRow');
            $this->cache()->save($sRowCacheId, $aRow);
            Phpfox::getLib('cache')->group('attachment', $sRowCacheId);
        }
			
		(($sPlugin = Phpfox_Plugin::get('attachment.service_attachment_hasaccess_end')) ? eval($sPlugin) : false);
        
        if (!isset($aRow['user_id'])) {
            return false;
        }
        
        if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('attachment.' . $sUserPerm)) || Phpfox::getUserParam('attachment.' . $sGlobalPerm) || Phpfox::isAdmin()) {
            return $aRow['user_id'];
        }
		
		return false;
	}
    
    /**
     * @param string $sMethod
     * @param array $aArguments
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('attachment.service_attachment__call'))
		{
			eval($sPlugin);
            return null;
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}