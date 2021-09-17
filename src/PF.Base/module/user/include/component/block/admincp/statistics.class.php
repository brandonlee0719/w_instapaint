<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Admincp_Statistics
 */
class User_Component_Block_Admincp_Statistics extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iUser = $this->request()->get('iUser');
		$aUser = Phpfox::getService('user')->get($iUser, true);
		$aFriends = Phpfox::callback('friend.getUserStatsForAdmin',$aUser['user_id']);
		$aDefaultStats = array(
		    array(
                'name' => $aFriends['total_name'],
                'total' => $aFriends['total_value']
            ),
            array(
                'name' => _p('spam_count'),
                'total' => $aUser['total_spam']
            )
        );
		$aStats = Phpfox::getService('user')->getUserStatistics($iUser);
        $aActivityPoints = array();
        $aActivityPoints['activity_total'] = array(
            _p('total_activity') => $aUser['activity_total']
        );
        $aActivityPoints['activity_points'] = array(
            _p('total_activity_points') => $aUser['activity_points']
        );

        $aMassActivityCallback = Phpfox::massCallback('getActivityPointField');
        foreach ($aMassActivityCallback as $aMassActivityCallbackModules)
        {
            foreach ($aMassActivityCallbackModules as $sName => $sValue)
            {
                $aActivityPoints[$sValue] = array(
                    $sName => $aUser[$sValue]
                );
            }
        }

        $this->template()->assign(array(
				'aUser' => $aUser,
                'aStats' => array_merge($aDefaultStats,$aStats),
                'aActivityPoints' => $aActivityPoints
			)
		);

		return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_filter_clean')) ? eval($sPlugin) : false);
	}
}
