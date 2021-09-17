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
 * @version 		$Id: process.class.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
class Attachment_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{		
		$this->_sTable = Phpfox::getT('attachment');
	}
    
    /**
     * Add new attachment
     * @param array $aVals
     *
     * @return int
     */
	public function add($aVals)
	{		
		$aVals = array_merge($aVals, array(
				'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
			)
		);
		
		$aInsert = array(
			'category_id' => $aVals['category'],
			'link_id' => (isset($aVals['link_id']) ? (int) $aVals['link_id'] : 0),
			'user_id' => Phpfox::getUserId(),
			'time_stamp' => PHPFOX_TIME,
			'file_name' => (empty($aVals['file_name']) ? null : $aVals['file_name']),
			'extension' => (empty($aVals['extension']) ? null : $aVals['extension']),
			'is_image' => ((isset($aVals['is_image']) && $aVals['is_image']) ? 1 : 0),
			'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
		);

		$iId = $this->database()->insert(Phpfox::getT('attachment'), $aInsert);

		// Update user activity
		Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'attachment');
		
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_add')) ? eval($sPlugin) : false);
		
		return $iId;
	}
    
    /**
     * Update an exist attachment
     * @param array $aVals
     * @param int $iId
     *
     * @return bool
     */
	public function update($aVals, $iId)
	{
		return $this->database()->update(Phpfox::getT('attachment'), $aVals, "attachment_id = " . $iId);
	}
    
    /**
     * @param int $sId
     * @param int $iUserId
     * @param int $iItemId
     */
	public function updateItemId($sId, $iUserId, $iItemId)
	{
		$aIds = explode(',', $sId);
		foreach ($aIds as $iId)
		{
			$iId = trim($iId);
			if (empty($iId) || !is_numeric($iId))
			{
				continue;
			}
			
			$aAttachment = $this->database()->select('*')
				->from(Phpfox::getT('attachment'))
				->where('attachment_id = ' . (int) $iId)
				->execute('getSlaveRow');
			if ($aAttachment) {
                $this->database()->update(Phpfox::getT('attachment'), array('item_id' => $iItemId), "attachment_id = " . $iId . " AND user_id = " . $iUserId . "");

                $this->updateItemCount($aAttachment['category_id'], $iId, '+');
            }
		}
	}
    
    /**
     * Update description of an attachment
     * @param int $iId
     * @param int $iUserId
     * @param string $sDescription
     *
     * @return bool
     */
	public function updateDescription($iId, $iUserId, $sDescription)
	{		
		$this->database()->update(Phpfox::getT('attachment'), array('description' => Phpfox::getLib('parse.input')->clean($sDescription, 255)), "attachment_id = " . $iId . " AND user_id = " . $iUserId . "");
		
		return true;
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function updateCounter($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_updatecounter')) ? eval($sPlugin) : false);

        $this->database()->update($this->_sTable, ['counter' => 'counter + 1'], ['attachment_id' => $iId], false);
		
		return true;
	}
    
    /**
     * @param int  $iId
     * @param bool $bRemove
     *
     * @return bool
     */
	public function updateInline($iId, $bRemove = false)
	{
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_updateinline')) ? eval($sPlugin) : false);
		
		$this->database()->update($this->_sTable, array('is_inline' => ($bRemove ? 0 : 1)), 'attachment_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());
		
		return true;
	}
	
	/**
	 * @todo Need to lower the total_attachment for items once it has been deleted.
	 *
	 * @param int $iUserId
	 * @param int $iItemId
	 * @param string $sCategory
	 * @return bool|null
	 */
	public function deleteForItem($iUserId = null, $iItemId, $sCategory)
	{		
		$aRows = $this->database()->select('user_id, attachment_id, destination, server_id')
			->from($this->_sTable)
			->where("item_id = " . $iItemId . " AND category_id = '" . $this->database()->escape($sCategory) . "'". ($iUserId !== null ? " AND user_id = " . $iUserId . "" : ''))
			->execute('getSlaveRows');
		
		if (!count($aRows))
		{
			return false;
		}
		
		$aFileSizes = array();
		foreach ($aRows as $aRow)
		{
			$sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '_thumb');
			$sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '_view');
			$sActualImage = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '');
			
			if (!isset($aFileSizes[$aRow['user_id']]))
			{
				$aFileSizes[$aRow['user_id']] = 0;
			}
			
			if(Phpfox::getParam('core.allow_cdn') && $aRow['server_id'] > 0)
		    {
				$aFilesToDelete = array($sThumbnail, $sViewImage, $sActualImage);
				foreach($aFilesToDelete as $sFilePath)
				{
					// Get the file size stored when the photo was uploaded
					$sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_attachment'), Phpfox::getParam('core.url_attachment'), $sFilePath));
					
					$aHeaders = get_headers($sTempUrl, true);
					if(preg_match('/200 OK/i', $aHeaders[0]))
					{
						$aFileSizes[$aRow['user_id']] += (int) $aHeaders["Content-Length"];
					}
					
					Phpfox::getLib('cdn')->remove($sFilePath);
				}
		    }
		    else
		    {				
				if (file_exists($sThumbnail))
				{
					$aFileSizes[$aRow['user_id']] += filesize($sThumbnail);
					Phpfox_File::instance()->unlink($sThumbnail);
				}
				
				if (file_exists($sViewImage))
				{
					$aFileSizes[$aRow['user_id']] += filesize($sViewImage);
					Phpfox_File::instance()->unlink($sViewImage);
				}

				if (file_exists($sActualImage))
				{
					$aFileSizes[$aRow['user_id']] += filesize($sActualImage);
					Phpfox_File::instance()->unlink($sActualImage);
				}
			}
			
			// Delete attachments for this specific item and category
			$this->database()->delete($this->_sTable, 'attachment_id = ' . $aRow['attachment_id']);
			
			// Update user activity
			Phpfox::getService('user.activity')->update($aRow['user_id'], 'attachment', '-');
		}
		
		foreach ($aFileSizes as $iUserId => $iFileSizes)
		{
			Phpfox::getService('user.space')->update($iUserId, 'attachment', $iFileSizes, '-');
		}		
		
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_deleteforitem')) ? eval($sPlugin) : false);
        return null;
	}
    
    /**
     * Delete an attachment
     * @param int $iUserId
     * @param int $iId
     *
     * @return bool
     */
	public function delete($iUserId, $iId)
	{
		$aRow = $this->database()->select('*')
			->from($this->_sTable)
			->where('attachment_id = ' . (int) $iId . ' AND user_id = ' . (int) $iUserId)
			->execute('getSlaveRow');
		
		if (!empty($aRow['destination']))
		{
			$iFileSizes = 0;
			$sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '_thumb');
			$sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '_view');
			$sActualImage = Phpfox::getParam('core.dir_attachment') . sprintf($aRow['destination'], '');
			
			if(Phpfox::getParam('core.allow_cdn') && $aRow['server_id'] > 0)
		    {		
				$aFilesToDelete = array($sThumbnail, $sViewImage, $sActualImage);
				foreach($aFilesToDelete as $sFilePath)
				{
					// Get the file size stored when the photo was uploaded
					$sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_attachment'), Phpfox::getParam('core.url_attachment'), $sFilePath));

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_URL            => $sTempUrl
                    ));
                    curl_exec($curl);
                    if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
                        $iFileSizes += (int)curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                    }
                    curl_close($curl);
					Phpfox::getLib('cdn')->remove($sFilePath);
				}
		    }
		    else
		    {
				if (file_exists($sThumbnail))
				{
					$iFileSizes += filesize($sThumbnail);
					Phpfox_File::instance()->unlink($sThumbnail);
				}
				
				if (file_exists($sViewImage))
				{
					$iFileSizes += filesize($sViewImage);
					Phpfox_File::instance()->unlink($sViewImage);
				}

				if (file_exists($sActualImage))
				{
					$iFileSizes += filesize($sActualImage);
					Phpfox_File::instance()->unlink($sActualImage);
				}
			}
			
			$this->updateItemCount($aRow['category_id'], $aRow['attachment_id'], '-');
			
			$this->database()->delete($this->_sTable, "destination = '" . $aRow['destination'] . "'");
			
			// Update user space usage
			if ($iFileSizes > 0)
			{
				Phpfox::getService('user.space')->update($iUserId, 'attachment', $iFileSizes, '-');
			}			
			
			Phpfox::getService('user.activity')->update($iUserId, 'attachment', '-');
			
			(($sPlugin = Phpfox_Plugin::get('attachment.service_process_delete')) ? eval($sPlugin) : false);
			
			return true;
		}	
		
		return false;
	}
    
    /**
     * @param int    $iCategory
     * @param int    $iId
     * @param string $sType
     *
     * @return bool
     */
	public function updateItemCount($iCategory, $iId, $sType = '+')
	{		
		if (!Phpfox::hasCallback($iCategory, 'getAttachmentField'))
		{
			return false;
		}
		
		list($sTable, $sField) = Phpfox::callback($iCategory . '.getAttachmentField');
		if ($sField === false)
		{
			return false;
		}
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_updateitemcount_category')) ? eval($sPlugin) : false);
	
		$aRow = $this->database()->select("t.{$sField}, t.total_attachment")
			->from($this->_sTable, 'a')
			->leftJoin(Phpfox::getT($sTable), 't', "t.{$sField} = a.item_id")
			->where("a.attachment_id = " . (int) $iId . "")
			->execute('getSlaveRow');
			
		if (!isset($aRow[$sField]))
		{
			return false;
		}
		
		$iCnt = $aRow['total_attachment'];
		if ($sType == '+')
		{
			$iCnt = ($iCnt + 1);
		}
		else 
		{
			$iCnt = ($iCnt - 1);
			if ($iCnt < 0)
			{
				$iCnt = 0;
			}
		}		

        $this->database()->update(Phpfox::getT($sTable), ['total_attachment' => $iCnt], [$sField => $aRow[$sField]]);
		
		(($sPlugin = Phpfox_Plugin::get('attachment.service_process_updateitemcount')) ? eval($sPlugin) : false);
		
		return true;
	}
    
    /**
     * @param int $iId
     * @param int $iType
     */
	public function updateActivity($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
	
		$this->database()->update(Phpfox::getT('attachment_type'), array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'extension = \'' . $iId . '\'');
		
		$this->cache()->remove();
	}
    
    /**
     * @param array $aVals
     * @param null  $sExt
     *
     * @return bool
     */
	public function addType($aVals, $sExt = null)
	{
		$aForm = array(
			'extension' => array(
				'type' => 'string:required',
				'message' => _p('provide_an_extension')
			),
			'mime_type' => array(
				'type' => 'string:required',
				'message' => _p('provide_a_mime_type')
			),
			'is_image' => array(
				'type' => 'int:required'
			),
			'is_active' => array(
				'type' => 'int:required'
			)
		);
		
		$aVals = $this->validator()->process($aForm, $aVals);
		if (strpos($aVals['extension'], '.') !== false)
		{
			Phpfox_Error::set(_p('invalid_file_extension'));
		}
		if (!Phpfox_Error::isPassed())
		{
			return false;
		}
		
		$iOldType = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('attachment_type'))
			->where('extension = \'' . $this->database()->escape($aVals['extension']) . '\'')
			->execute('getSlaveField');

		
		if ($sExt === null)
		{
            if ($iOldType)
            {
                return Phpfox_Error::set(_p('this_extension_already_exists'));
            }
			$aVals['added'] = PHPFOX_TIME;
			
			$this->database()->insert(Phpfox::getT('attachment_type'), $aVals);
		}
		else 
		{
			$this->database()->update(Phpfox::getT('attachment_type'), $aVals, 'extension = \'' . $this->database()->escape($sExt) . '\'');
		}
		
		$this->cache()->remove();
		
		return true;
	}
    
    /**
     * @param string $sExt
     * @param array $aVals
     *
     * @return bool
     */
	public function updateType($sExt, $aVals)
	{
		return $this->addType($aVals, $sExt);
	}
    
    /**
     * @param string $sExt
     *
     * @return bool
     */
	public function deleteType($sExt)
	{
		$this->database()->delete(Phpfox::getT('attachment_type'), 'extension = \'' . $this->database()->escape($sExt) . '\'');
		
		$this->cache()->remove();
		
		return true;
	}

    /**
     * @deprecated
     */
	public function process($aAttachments, $iUserId, $iItemId)
	{
	}

    /**
     * Clone attachments
     *
     * @param $sIds
     * @return string
     */
	public function cloneAttachments($sIds)
    {
        $aNewAttachmentIds = [];

        foreach (explode(',', trim($sIds, ',')) as $iId) {
            if (!$iId) {
                continue;
            }
            $aAttachment = db()->select('view_id, category_id, link_id, user_id, time_stamp, file_name, file_size, destination, extension, video_duration, is_inline, is_image, is_video, server_id')->from(':attachment')->where(['attachment_id' => $iId])->executeRow();
            $aNewAttachmentIds[] = db()->insert(':attachment', $aAttachment);
        }

        return implode(',', $aNewAttachmentIds);
    }

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
     * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('attachment.service_process__call'))
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