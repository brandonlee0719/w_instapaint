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
 * @package 		Phpfox_Service
 * @version 		$Id: service.class.php 67 2009-01-20 11:32:45Z Raymond_Benc $
 */
class Notification_Service_Notification extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('notification');	
	}
	
	public function getForBrowse($iPage = 0, $iPageTotal = 10)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'n')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
			->where('n.user_id = ' . Phpfox::getUserId() . '')
			->execute('getSlaveField');
		$aRows = $this->database()->select('n.*, n.user_id as item_user_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'n')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
			->where('n.user_id = ' . Phpfox::getUserId() . '')
			->order('n.time_stamp DESC')
			->limit($iPage, $iPageTotal, $iCnt, false, false)
			->execute('getSlaveRows');

		$sIds = '';
		$aNotifications = array();
		foreach ($aRows as $aRow)
		{
			$sIds .= (int) $aRow['notification_id'] . ',';
			
			$iDate = Phpfox::getTime('dmy', $aRow['time_stamp']);		
				
			if ($iDate == Phpfox::getTime('dmy', PHPFOX_TIME))
			{
				$iDate = _p('today');
			}
			elseif ($iDate == Phpfox::getTime('dmy', (PHPFOX_TIME - 86400)))
			{
				$iDate = _p('yesterday');
			}
			else 
			{
				 $iDate = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']);
			}

			$app = null;
			$app_key_name = null;
			if (strpos($aRow['type_id'], '/')) {
				list($app, $app_key_name) = explode('/', $aRow['type_id']);
				if (app()->exists($app)) {
					$app = app($app);
				}
			}
			if ($app !== null) {
				$aRow['link'] = '';
				$aRow['message'] = '';
				$aRow['custom_image'] = '';

				if ($app->notifications && isset($app->notifications->{$app_key_name})) {
					$notification = $app->notifications->{$app_key_name};
                    Core\Event::trigger('notification_map_'.$app->id, $app_key_name, $aRow, $notification);
					$aRow['message'] = _p($notification->message, ['user_full_name' => $aRow['full_name']]);
					$aRow['link'] = url(str_replace(':id', $aRow['item_id'], $notification->url));
					$aRow['custom_icon'] = $notification->icon;

					$aNotifications[$iDate][] = $aRow;

					continue;
				}
			}

			$aParts1 = explode('.', $aRow['type_id']);
			$sModule = $aParts1[0];
			if (strpos($sModule, '_'))
			{
				$aParts = explode('_', $sModule);
				$sModule = $aParts[0];
			}

			if (Phpfox::isModule($sModule)) {
				if (substr($aRow['type_id'], 0, 8) != 'comment_' && !Phpfox::hasCallback($aRow['type_id'], 'getNotification')) {
					$aRow['link'] = '#';
					$aRow['message'] = '1. Notification is missing a callback. [' . $aRow['type_id'] . '::getNotification]';

					$aNotifications[ $iDate ][] = $aRow;

					continue;
				}

				if (substr($aRow['type_id'], 0, 8) == 'comment_' && substr($aRow['type_id'], 0, 12) != 'comment_feed' && !Phpfox::hasCallback(substr_replace($aRow['type_id'], '', 0, 8), 'getCommentNotification')) {
					if (Phpfox::isModule(substr_replace($aRow['type_id'], '', 0, 8))) {
						$aRow['link'] = '#';
						$aRow['message'] = 'Notification is missing a callback. [' . substr_replace($aRow['type_id'], '', 0, 8) . '::getCommentNotification]';

						$aNotifications[ $iDate ][] = $aRow;

                        continue;
                    }
				}


				if (($aCallBack = Phpfox::callback($aRow['type_id'] . '.getNotification', $aRow))) {
				    if ($aCallBack === false) {
				        continue;
                    }
					if (!isset($aCallBack['message'])) {
						$aRow['link'] = '#';
						$aRow['message'] = 'Notification is missing a message/link param. [' . $aRow['type_id'] . '::getNotification]';
					}
                    $aCallBack['message'] = Phpfox::getLib('parse.output')->cleanScriptTag($aCallBack['message']);
					$aNotifications[ $iDate ][] = array_merge($aRow, (array)$aCallBack);
				}
			}
		}
		
		$sIds = rtrim($sIds, ',');
		
		if (!empty($sIds))
		{
			$this->database()->update(Phpfox::getT('notification'), array('is_seen' => '1'), 'notification_id IN(' . $sIds . ')');
		}
		
		return array($iCnt, $aNotifications);
	}
	
	public function get()
	{
		static $aNotifications = null;
		
		if (is_array($aNotifications))
		{
			return $aNotifications;
		}

		$this->database()->select('n_sub.type_id, n_sub.item_id, COUNT(n_sub.notification_id) AS total_extra, MAX(n_sub.time_stamp) AS max_time_stamp')
            ->from($this->_sTable, 'n_sub')
            ->where(['n_sub.user_id' => Phpfox::getUserId()])
            ->group('n_sub.type_id, n_sub.item_id')
            ->union();
		$aGetRows = $this->database()->select('n.*, n_sub.total_extra as total_extra, n.user_id as item_user_id, ' . Phpfox::getUserField())
            ->unionFrom('n_sub')
			->join($this->_sTable, 'n', 'n_sub.type_id = n.type_id AND n_sub.item_id = n.item_id AND n_sub.max_time_stamp = n.time_stamp')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
			->where('n.user_id = ' . Phpfox::getUserId() . '')
			->order('n.is_seen ASC, n.time_stamp DESC')
			->limit(20)
			->execute('getSlaveRows');

		$aRows = array();
		foreach ($aGetRows as $aGetRow)
		{
			$aRows[(int) $aGetRow['notification_id']] = $aGetRow;
		}
		arsort($aRows);

		$aNotifications = array();
		foreach ($aRows as $aRow)
		{
			$aParts1 = explode('.', $aRow['type_id']);
			$sModule = $aParts1[0];
			if (strpos($sModule, '_'))
			{
				$aParts = explode('_', $sModule);
				$sModule = $aParts[0];
			}
			$app = null;
			$app_key_name = null;
			if (strpos($aRow['type_id'], '/')) {
				list($app, $app_key_name) = explode('/', $aRow['type_id']);
				if (app()->exists($app)) {
					$app = app($app);
				}
			}

			if ($app !== null && $app->notifications && isset($app->notifications->{$app_key_name})) {
				$notification = $app->notifications->{$app_key_name};
                Core\Event::trigger('notification_map_'.$app->id, $app_key_name, $aRow, $notification);
				$aRow['message'] = _p($notification->message, ['user_full_name' => $aRow['full_name']]);
				$aRow['link'] = url(str_replace(':id', $aRow['item_id'], $notification->url));
				$aRow['custom_icon'] = $notification->icon;

				$aNotifications[] = $aRow;
			}
			elseif (Phpfox::isModule($sModule))
			{					
				if ((int) $aRow['total_extra'] > 1)
				{
					$aExtra = $this->database()->select('n.owner_user_id, n.time_stamp, n.is_seen, u.full_name')
						->from($this->_sTable, 'n')
						->join(Phpfox::getT('user'), 'u', 'u.user_id = n.owner_user_id')
						->where('n.type_id = \'' . $this->database()->escape($aRow['type_id']) . '\' AND n.item_id = ' . (int) $aRow['item_id'])
						->group('u.user_id', true)
						->order('n.time_stamp DESC')
						->limit(10)
						->execute('getSlaveRows');

					foreach ($aExtra as $iKey => $aExtraUser)
					{
						if ($aExtraUser['owner_user_id'] == $aRow['user_id'])
						{
							unset($aExtra[$iKey]);
						}					

						if (!$aRow['is_seen'] && $aExtraUser['is_seen'])
						{
							unset($aExtra[$iKey]);
						}
					}

					if (count($aExtra))
					{
						$aRow['extra_users'] = $aExtra;			
					}
				}	

				if (substr($aRow['type_id'], 0, 8) != 'comment_' && !Phpfox::hasCallback($aRow['type_id'], 'getNotification'))
				{
					$aCallBack['link'] = '#';
					$aCallBack['message'] = '2. Notification is missing a callback. [' . $aRow['type_id'] . '::getNotification]';
				}		
				elseif (substr($aRow['type_id'], 0, 8) == 'comment_' && substr($aRow['type_id'], 0, 12) != 'comment_feed' && !Phpfox::hasCallback(substr_replace($aRow['type_id'], '', 0, 8), 'getCommentNotification') && Phpfox::isModule(substr_replace($aRow['type_id'], '', 0, 8)))
				{
                    $aCallBack['link'] = '#';
                    $aCallBack['message'] = 'Notification is missing a callback. [' . substr_replace($aRow['type_id'], '', 0, 8) . '::getCommentNotification]';
				}
				else
				{
					$aCallBack = Phpfox::callback($aRow['type_id'] . '.getNotification', $aRow);
					if ($aCallBack === false)
					{
					    if (substr($aRow['type_id'], 0, 8) != 'comment_') {
                            $this->database()->delete($this->_sTable, 'notification_id = ' . (int) $aRow['notification_id']);
                        }

						continue;
					}

					$aRow['final_module'] = Phpfox_Module::instance()->sFinalModuleCallback;
					if ($aRow['final_module'] == 'photo')
					{
						$aCallBack['link'] = $aCallBack['link'] . 'userid_' . Phpfox::getUserId() . '/';
					}
				}
				$aNotification = array_merge($aRow, (array) $aCallBack);
				if (!empty($aNotification['message'])) {
                    $aNotification['message'] = Phpfox::getLib('parse.bbcode')->removeTagText($aNotification['message']);
                    $aNotification['message'] = Phpfox::getService('ban.word')->clean($aNotification['message']);
                    $aNotification['message'] = Phpfox::getLib('parse.output')->cleanScriptTag($aNotification['message']);
                }
				$aNotifications[] = $aNotification;
			}
		}

		$this->database()->update($this->_sTable, array('is_seen' => '1'), array_merge([
            'user_id' => Phpfox::getUserId()
        ], empty($aNotifications) ? [] : [
            'notification_id' => ['in' => implode(',', array_column($aNotifications, 'notification_id'))]
        ]));

		return $aNotifications;
	}	
	
	public function getUnseenTotal()
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'n')			
			->where('n.user_id = ' . (int) Phpfox::getUserId() . ' AND n.is_seen = 0')			
			->execute('getSlaveField');
					
		return $iCnt;
	}
	
	public function getUsers($aNotification, $bRemove = false)
	{
		if (isset($aNotification['extra_users']) && is_array($aNotification['extra_users']) && count($aNotification['extra_users']))
		{
			$iFriendLimit = 2;
			
			$iCnt = 0;
			$sStr = '';
			foreach ($aNotification['extra_users'] as $aUser)
			{	
				if (isset($aUser['owner_user_id']) && $aUser['owner_user_id'] == Phpfox::getUserId() && $aNotification['item_user_id'] == Phpfox::getUserId())
				{
					continue;
				}
				
				$iCnt++;
				
				if ($iCnt > $iFriendLimit)
				{
					$sExtraTotalUsers = (int) (count($aNotification['extra_users']) - $iFriendLimit);
					
					break;
				}			
								
				$sStr .= '<span class="drop_data_user">' . ((isset($aUser['user_id']) && $aUser['user_id'] == Phpfox::getUserId()) ? _p('you') : Phpfox::getLib('parse.output')->shorten($aUser['full_name'], 0)) . '</span>, ';
			}
			
			if ($bRemove)
			{	
				if (isset($sExtraTotalUsers))
				{
					if ($sExtraTotalUsers === 1)
					{
						$sStr = $sStr . ' ' . _p('and') . ' ' . $sExtraTotalUsers . ' ' . _p('other') . ' ';
					}
					else 
					{
						$sStr = $sStr . ' ' . _p('and') . ' ' . $sExtraTotalUsers . ' ' . _p('others') . ' ';
					}					
				}
				else 
				{
					$sStr = rtrim($sStr, ', ');
					$aStr = explode(',', $sStr);
					$sNew = '';
					$iStr = 0;
					foreach ($aStr as $sNewStr)
					{
						if ((count($aStr) - 1) == ($iStr + 1))
						{
							$sNew .= trim($sNewStr) . ' ' . _p('and') . ' ';
							
							continue;
						}
						
						$iStr++;
						
						$sNew .= trim($sNewStr) . ', ';
					}					
					
					$sStr = rtrim($sNew, ', ');
				}
			}
			else 
			{
				if (isset($sExtraTotalUsers))
				{
					if ($sExtraTotalUsers === 1)
					{
						$sStr = $sStr . ' <span class="drop_data_user">' . ((isset($aNotification['user_id']) && $aNotification['user_id'] == Phpfox::getUserId()) ? 'You' : Phpfox::getLib('parse.output')->shorten($aNotification['full_name'], 0)) . '</span> ' . _p('and') . ' ' . $sExtraTotalUsers . ' ' . _p('other') . ' ';
					}
					else 
					{
						$sStr = $sStr . ' <span class="drop_data_user">' . ((isset($aNotification['user_id']) && $aNotification['user_id'] == Phpfox::getUserId()) ? 'You' : Phpfox::getLib('parse.output')->shorten($aNotification['full_name'], 0)) . '</span> ' . _p('and') . ' ' . $sExtraTotalUsers . ' ' . _p('others') . ' ';
					}				
				}
				else 
				{
					$sStr = (empty($sStr) ? '' : rtrim($sStr, ', ') . ' ' . _p('and')) . ' <span class="drop_data_user">' . ((isset($aNotification['user_id']) && $aNotification['user_id'] == Phpfox::getUserId()) ? 'You' : Phpfox::getLib('parse.output')->shorten($aNotification['full_name'], 0)) . '</span>';
				}
			}
			
			return $sStr;
		}
		
		$sStr = '<span class="drop_data_user">' . ((isset($aNotification['user_id']) && $aNotification['user_id'] == Phpfox::getUserId()) ? _p('you') : Phpfox::getLib('parse.output')->shorten($aNotification['full_name'], 0)) . '</span>';
		
		return $sStr;
	}

    /**
     * @param $sType
     * @param $iItemId
     * @param $iUserId
     * @return array|int|string
     */
	public function checkExisted($sType, $iItemId, $iUserId)
    {
        return db()->select('notification_id')
                    ->from(':notification')
                    ->where('type_id =\''.$sType.'\' AND item_id = '.(int)$iItemId.' AND user_id = '.(int)$iUserId)
                    ->execute('getSlaveField');
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
		if ($sPlugin = Phpfox_Plugin::get('notification.service_notification__call'))
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