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
 * @package  		Module_Track
 * @version 		$Id: track.class.php 5914 2013-05-13 08:38:15Z Raymond_Benc $
 */
class Track_Service_Track extends Phpfox_Service 
{	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		(($sPlugin = Phpfox_Plugin::get('track.service_track___construct')) ? eval($sPlugin) : false);	
	}	
	
	public function getLatestUsers($sType, $iId, $iUserId)
	{			
		if (Phpfox::getParam('track.cache_recently_viewed_by_timeout') > 0)
		{
			$sCacheId = $this->cache()->set(array('track', $sType . '_' . $iId));
            Phpfox::getLib('cache')->group(  'track', $sCacheId);
			
			if (!($aTracks = $this->cache()->get($sCacheId, Phpfox::getParam('track.cache_recently_viewed_by_timeout') * 60))) // Cache is in minutes 
			{
				$aTracks = Phpfox::callback($sType . '.getLatestTrackUsers', $iId, $iUserId);
				
				$this->cache()->save($sCacheId, $aTracks);				
			}

			if (is_bool($aTracks))
			{
				$aTracks = array();
			}
			
			return $aTracks;
		}
		
		return Phpfox::callback($sType . '.getLatestTrackUsers', $iId, $iUserId);
	}
	
	public function moveTrackData(){
	    $aTableLists = [
	      'ad_track',
	      'blog_track',
	      'forum_thread_track',
	      'forum_track',
	      'page_track',
	      'photo_track',
	      'poll_track',
	      'quiz_track',
	      'user_track',
        ];
        $iCnt = 0;
        foreach ($aTableLists as $sTable){
            $iCnt += $this->moveTrackOnTable($sTable);
        }
        if ($iCnt > 0){
            $this->moveTrackData();
        }
    }
    
    private function moveTrackOnTable($sTable){
        $aDataRows = $this->database()->select('*')
            ->from(Phpfox::getT($sTable))
            ->limit(1000)
            ->execute('getSlaveRows');
        if (count($aDataRows) == 0){
            return 0;
        }
        $sTypeId = '';
        $track_id = 'track_id';
        $item_id = 'item_id';
        $user_id = 'user_id';
        $ip_address = 'ip_address';
        $time_stamp = 'time_stamp';
        switch ($sTable){
            case 'ad_track':
                $sTypeId = 'ad';
                $item_id = 'ad_id';
                break;
            case 'blog_track':
                $sTypeId = 'blog';
                $track_id = '';
                $ip_address = '';
                break;
            case 'forum_thread_track':
                $sTypeId = 'forum_thread';
                $item_id = 'thread_id';
                $track_id = '';
                $ip_address = '';
                break;
            case 'forum_track':
                $sTypeId = 'forum';
                $item_id = 'forum_id';
                $track_id = '';
                $ip_address = '';
                break;
            case 'page_track':
                $sTypeId = 'page';
                $track_id = '';
                $ip_address = '';
                break;
            case 'photo_track':
                $sTypeId = 'photo';
                $track_id = '';
                $ip_address = '';
                break;
            case 'poll_track':
                $sTypeId = 'poll';
                $track_id = '';
                $ip_address = '';
                break;
            case 'quiz_track':
                $sTypeId = 'quiz';
                $track_id = '';
                $ip_address = '';
                break;
            case 'user_track':
                $sTypeId = 'user';
                $track_id = '';
                $ip_address = '';
                break;
            default:
                break;
        }
        if (empty($sTypeId)){
            return false;
        }
        foreach ($aDataRows as $aDataRow){
            $dataIPAddress = (empty($ip_address) ? '' : $aDataRow[$ip_address]);
            $aInsert = [
                'type_id' => $sTypeId,
                'item_id' => $aDataRow[$item_id],
                'user_id' => $aDataRow[$user_id],
                'ip_address' => $dataIPAddress,
                'time_stamp' => $aDataRow[$time_stamp]
            ];
            $this->database()->insert(':track', $aInsert);
            if (empty($track_id)){
                $this->database()->delete(Phpfox::getT($sTable), "$track_id=" . (int) $aDataRow[$track_id]);
            } else {
                $this->database()->delete(Phpfox::getT($sTable), "$item_id=" . (int) $aDataRow[$item_id] . " AND $user_id=" . (int) $aDataRow[$user_id]);
            }
        }
        $iRemainingItems = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT($sTable))
            ->execute('getSlaveField');
        return $iRemainingItems;
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
		if ($sPlugin = Phpfox_Plugin::get('track.service_track___call'))
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