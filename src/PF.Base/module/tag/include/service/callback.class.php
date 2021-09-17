<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Tag_Service_Callback
 */
class Tag_Service_Callback extends Phpfox_Service 
{
	public function onDeleteUser($iUser)
	{
	    $this->database()->delete(Phpfox::getT('tag'), 'user_id = ' . (int) $iUser);
	}		
	
	public function hideBlockCloud()
	{
		return array(
			'table' => 'user_dashboard'
		);		
	}	
	
	public function getBlockDetailsCloud()
	{
		return array(
			'title' => _p('tag_cloud')
		);
	}

	public function getCommentNotificationFeed($aNotification)
    {
        $aRow = $this->database()->select('f.feed_id, f.item_id, u.user_name, u.full_name, u2.user_name as wall_user_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('feed'), 'f', 'f.feed_id = c.author')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = f.parent_user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['full_name'])) {
            return false;
        }
        $sPhrase = _p('user_name_tagged_you_in_a_comment', ['user_name' => $aRow['full_name']]);

        return [
            'link' => Phpfox_Url::instance()->makeUrl($aRow['wall_user_name'], ['comment-id' => $aRow['item_id']]),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
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
		if ($sPlugin = Phpfox_Plugin::get('tag.service_callback__call'))
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
