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
class Like_Component_Block_Browse extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iPage = $this->request()->getInt('page', 1); // pagination page
        $iLikesPerPage = Phpfox::getParam('core.items_per_page', 10); // max likes per page

        $aLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'),
            $this->request()->getInt('item_id'), $this->request()->getInt('feed_table_prefix', ''), false, $iPage,
            $iLikesPerPage);
        $iTotalLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'),
            $this->request()->getInt('item_id'), $this->request()->getInt('feed_table_prefix', ''), true);

        $sErrorMessage = '';
        if ($this->request()->get('type_id') == 'pages') {
            $aPage = Phpfox::getService('pages')->getPage($this->request()->getInt('item_id'));
            if (!count($aLikes)) {
                if ($aPage['type_id'] == 3) {
                    $sErrorMessage = _p('this_group_has_no_members');
                } else {
                    $sErrorMessage = _p('nobody_likes_this');
                }
            }
        }

        $bIsPageAdmin = false;
        if ($this->request()->get('type_id') == 'pages' && Phpfox::getService('pages')->isAdmin($this->request()->getInt('item_id'))) {
            $bIsPageAdmin = true;
        }

        // Pagination configuration
        $pager = Phpfox_Pager::instance();
        $pager->set(array(
            'page' => $iPage,
            'size' => $iLikesPerPage,
            'count' => $iTotalLikes,
            'paging_mode' => 'loadmore',
            'ajax_paging' => [
                'block' => 'like.browse',
                'params' => [
                    'type_id' => $this->request()->get('type_id'),
                    'item_id' => $this->request()->getInt('item_id'),
                    'feed_table_prefix' => $this->request()->getInt('feed_table_prefix', '')
                ],
                'container' => '.like-browse-container'
            ]
        ));

        (($sPlugin = Phpfox_Plugin::get('like.component_block_browse_process')) ? eval($sPlugin) : false);

        $this->template()->assign(array(
                'aLikes' => $aLikes,
                'sErrorMessage' => $sErrorMessage,
                'sItemType' => $this->request()->get('type_id'),
                'iItemId' => $this->request()->getInt('item_id'),
                'bIsPageAdmin' => $bIsPageAdmin,
                'bIsPaging' => $this->getParam('ajax_paging', 0),
                'hasPagingNext' => $iPage < $pager->getTotalPages()
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('like.component_block_browse_clean')) ? eval($sPlugin) : false);
    }
}
