<?php
defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Controller_Panel extends Phpfox_Component {
	public function process() {
		Phpfox::isUser(true);

		list($iCnt, $aFriends) = Phpfox::getService('friend.request')->get(0, 100);
		foreach ($aFriends as $key => $friend) {
			if ($friend['relation_data_id']) {
				$sRelationShipName = Phpfox::getService('custom.relation')->getRelationName($friend['relation_id']);
                if (isset($sRelationShipName) && !empty($sRelationShipName)){
                  $aFriends[$key]['relation_name'] = $sRelationShipName;
                } else {
                  //This relationship was removed
                  unset($aFriends[$key]);
                }
			}
		}
		$iTotalFriendRequest = count($aFriends);
        $iNumberNewFriendRequest = 0;
        foreach ($aFriends as $aFriend){
            if (isset($aFriend['is_seen']) && $aFriend['is_seen'] == 1){
                continue;
            }
            $iNumberNewFriendRequest++;
        }

        $sScript = '';
        if ($iNumberNewFriendRequest) {
            $sScript .= '$("span#js_total_new_friend_requests").html("' . $iNumberNewFriendRequest . '").show();';
        } else {
            $sScript = '$("span#js_total_new_friend_requests").hide();';
        }

        if ($iTotalFriendRequest) {
            $sScript .= '$("span#js_total_friend_requests").html("(' . $iTotalFriendRequest . ')").show();';
            $sScript .= '$("#js_view_all_requests").html("' . _p($iTotalFriendRequest > 1 ? 'view_all_number_requests' : 'view_all_number_request',
                    ['number' => $iTotalFriendRequest]) . '");';
        } else {
            $sScript = '$("span#js_total_friend_requests").hide();$("#js_view_all_requests").empty();';
        }

        $sScript = '<script>$Behavior.resetFriendRequestCount = function() {'. $sScript . '$Behavior.resetFriendRequestCount=function(){};};</script>';
		$this->template()->assign([
			'aFriends' => $aFriends,
			'sScript' => $sScript,
		]);

		//hide all blocks
        Phpfox_Module::instance()->resetBlocks();
	}
}