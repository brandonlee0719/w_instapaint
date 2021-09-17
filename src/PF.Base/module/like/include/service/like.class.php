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
 * @package 		Phpfox_Service
 * @version 		$Id: like.class.php 7054 2014-01-20 18:35:55Z Fern $
 */
class Like_Service_Like extends Phpfox_Service 
{
	private $_iTotalLikeCount = 0;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('like');	
	}
	
	public function getTotalLikes()
	{
		return $this->_iTotalLikeCount;
	}
	
	public function getLikesForFeed($sType, $iItemId, $bIsLiked = false, $iLimit = 4, $bLoadCount = false, $sFeedTablePrefix = '')
	{
		if (redis()->enabled()) {
			$likes = [];
			$rows = redis()->lrange('likes/' . $sType . '/' . $iItemId, 0, 1000);
			foreach ($rows as $user_id) {
				$likes[] = redis()->user($user_id);
			}

			return $likes;
		}

		$sWhere = '(l.type_id = \'' . $this->database()->escape(str_replace('-','_',$sType)) . '\' OR l.type_id = \'' . str_replace('_','-',$sType) . '\') AND l.item_id = ' . (int) $iItemId;

        if ($sType == 'app')
        {
            $sWhere .= " AND l.feed_table = '{$sFeedTablePrefix}feed'";
        }
		
		if (Phpfox::getParam('like.show_user_photos'))
		{
			$this->database()->where($sWhere);
		}
		else
		{
			$this->database()->where($sWhere . ' AND l.user_id != ' . Phpfox::getUserId());
		}
		
		$aRowLikes = $this->database()->select('l.*, ' . Phpfox::getUserField() .', a.time_stamp as action_time_stamp')
			->from($this->_sTable, 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')			
            ->leftJoin(Phpfox::getT('action'), 'a', 'a.item_id = l.item_id AND a.user_id = l.user_id AND a.item_type_id = \'' . str_replace('_', '-', $this->database()->escape($sType)) .'\'')
            ->group('u.user_id, l.like_id, action_time_stamp', true)
			->order('l.time_stamp DESC')
			->limit($iLimit)
			->execute('getSlaveRows');

		$aLikes = array();
        $aDontCount = array();
        foreach ($aRowLikes as $iKey => $aLike)
        {        	
            if (!empty($aLike['action_time_stamp']) && $aLike['action_time_stamp'] > $aLike['time_stamp'])
            {
                $aDontCount[] = $aLike['like_id'];

                continue;
            }
            
            $aLikes[$aLike['user_id']] = $aLike;
        }
		$this->_iTotalLikeCount = count($aLikes);
                
        if ($bLoadCount == true)
        {
            if (!empty($aDontCount))
            {
                $sWhere .= ' AND l.like_id NOT IN (' . implode(',', $aDontCount) . ')';
            }
            $this->_iTotalLikeCount = $this->database()->select('COUNT(*)')
                    ->from(Phpfox::getT('like'), 'l')
                    ->where($sWhere)
                    ->execute('getSlaveField') ;
        }
		return $aLikes;
	}
	
	public function getTotalLikeCount()
	{
		return $this->_iTotalLikeCount;
	}

    public function getLikes($sType, $iItemId, $sPrefix = '', $bGetCount = false, $iPage = 0, $iTotal = null)
	{
        $sPrefix = $sPrefix . 'feed';
        if ($sType == 'feed') {
            $this->database()->where('(l.type_id = "feed" OR l.type_id = "feed_comment") AND l.item_id = ' . (int)$iItemId);
        } else {
            $this->database()->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int)$iItemId . ($sType == 'app' ? " AND feed_table = '{$sPrefix}'" : ''));
        }
        $this->database()
            ->from(Phpfox::getT('like'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id');

	    if ($bGetCount) {
            return $this->database()->select('count(*)')->executeField();
        } else {
	        if ($iPage) {
                $this->database()->limit($iPage, $iTotal);
            }
            
            $aLikes = $this->database()->select(Phpfox::getUserField())
                ->group('u.user_id')
                ->order('u.full_name ASC')
                ->execute('getSlaveRows');

            return $aLikes;
        }
	}
	
	public function getForMembers($sType, $iItemId, $iLimit = null)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'), 'l')
			->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)
			->execute('getSlaveField');
		
		$aLikes = $this->database()->select('uf.total_friend, ' . Phpfox::getUserField())
			->from(Phpfox::getT('like'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
            ->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)
			->order('u.full_name ASC')
			->group('u.user_id')
			->limit(($iLimit === null ? 5 : $iLimit))
			->execute('getSlaveRows');
					
		return array($iCnt, $aLikes);		
	}

        public function didILike($sType, $iItemId, $aLikes = array(), $sPrefix = '')
        {
            $sType = str_replace('-', '_', $sType);
            if (empty($aLikes) || !is_array($aLikes))
            {
				$aLikes = $this->getLikes($sType, $iItemId, $sPrefix);
			}
            foreach ($aLikes as $aLike)
            {
                if ($aLike['user_id'] == Phpfox::getUserId())
                {
                    return true;
                }
            }
            return false;
        }
	
	/* This function gets all the likes  for a specific item.
	* It is used in the ajax component in the like module in the _loadLikes function.
	* @return array 
	* 		returns array(
	* 			'likes' => array(
	* 				'total' => 44,
	*				'phrase' => 'phrase in plain text, already parsed and ready to output'
	*			)
	*/
	public function getAll($sType, $iItem, $sPrefix = '')
	{
		$aLikes = $this->getLikes($sType, $iItem, $sPrefix);
		$aFeed = array('likes' => $aLikes);
		$aFeed['type_id'] = $sType;
		$aFeed['item_id'] = $iItem;
        $aFeed['feed_table_prefix'] = $sPrefix;
		$sLikePhrase = Phpfox::getService('feed')->getPhraseForLikes($aFeed);
		

		$aOut = array(
			'likes' => array(
				'total' => count($aLikes),
				'phrase' => $sLikePhrase
			)
		);
		
		return $aOut;		
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
		if ($sPlugin = Phpfox_Plugin::get('like.service_like__call'))
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