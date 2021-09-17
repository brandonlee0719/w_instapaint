<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Component
 */
class Friend_Component_Block_Mutual_Browse extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iPageSize = $this->getParam('core.items_per_page', 20);
        $iPage = $this->request()->getInt('page', 1);
        $iUserId = $this->request()->getInt('user_id');
        $aCond = array();
        $aCond[] = 'AND friend.user_id = ' . Phpfox::getUserId();

        list($iCnt, $aFriends) = Phpfox::getService('friend')->get($aCond, 'friend.time_stamp DESC', $iPage,
            $iPageSize, true, false, false, $iUserId);
        $sUserName = Phpfox::getService('friend')->getUserName($iUserId);
        Phpfox_Pager::instance()->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => 'loadmore',
            'ajax_paging' => [
                'block' => 'friend.mutual-browse',
                'params' => [
                    'page' => $iPage,
                    'user_id' => $iUserId
                ],
                'container' => '.js_friend_mutual_container'
            ]
        ));

        $this->template()->assign(array(
                'aFriends' => $aFriends,
                'iPage' => $iPage,
                'sUserName' => $sUserName,
                'iTotalMutualFriends' => $iCnt,
                'bIsPaging' => $this->getParam('ajax_paging', 0),
                'hasPagingNext' => $iPage < Phpfox::getLib('pager')->getTotalPages()
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
        (($sPlugin = Phpfox_Plugin::get('friend.component_block_mutual_browse_clean')) ? eval($sPlugin) : false);
    }
}