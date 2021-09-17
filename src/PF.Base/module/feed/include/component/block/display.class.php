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
 * @package         Module_Feed
 * @version         $Id: display.class.php 7270 2014-04-14 17:06:13Z Fern $
 */
class Feed_Component_Block_Display extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_PAGES_WIDGET') || defined('PHPFOX_VIDEOS_INTEGRATE_PAGE')) {
            return false;
        }

        if (defined('PHPFOX_IS_PAGES_VIEW') && ($this->request()->get('req3') == 'info' || $this->request()->get('req2') == 'info')) {
            return false;
        }

        $iUserId = $this->getParam('user_id');
        $aPage = $this->getParam('aPage');

        // Don't display the feed if approving users
        if (isset($aPage['page_id']) && ($this->request()->get('req3') == 'pending' || $this->request()->get('req2') == 'pending')) {
            return false;
        }
        if (isset($aPage['landing_page']) && $aPage['landing_page'] == 'info' &&
            (
                (empty($aPage['vanity_url']) && $this->request()->get('req3') == '') ||
                (!empty($aPage['vanity_url']) && ($this->request()->get('req2') == 'info' || $this->request()->get('req2') == ''))
            )
        ) {
            return false;
        }

        if (isset($aPage['page_id']) && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'groups') {
            $bGroupIsShareable = true;
            if (isset($aPage['reg_method'])) {
                $bGroupIsShareable = ($aPage['reg_method'] == 0) ? true : false;
            }
            $this->template()->assign([
                'bIsGroupMember'    => Phpfox::isAdmin() ? true : Core\Lib::appsGroup()->isMember($aPage['page_id']),
                'bGroupIsShareable' => $bGroupIsShareable
            ]);
        }

        $bForceFormOnly = $this->getParam('bForceFormOnly');
        if (isset($aPage['page_user_id']) && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            $bHasPerm = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aPage['page_id'],
                PHPFOX_PAGES_ITEM_TYPE . '.view_browse_updates');
            if ($bHasPerm == false) {
                return false;
            }
            $iUserId = $aPage['page_user_id'];

            /* Get all blocks for location 2 and 3 */

            $oBlock = Phpfox_Module::instance();
            $aExtraBlocks = array();
            $aBlocks = $oBlock->getModuleBlocks(1, true);
            $aBlocks = array_merge($aBlocks, $oBlock->getModuleBlocks(3, true));
            foreach ($aBlocks as $iKey => $sBlock) {
                switch ($sBlock) {
                    case 'pages.menu':
                    case 'pages.photo':
                        if ($sBlock == 'pages.menu') {
                            $aExtraBlocks[] = $sBlock;
                        }
                        unset($aBlocks[$iKey]);
                        break;
                }

            }
            $aBlocks = array_merge($aBlocks, $aExtraBlocks);
            $this->template()->assign(array('aLoadBlocks' => $aBlocks));
        }
        $bIsCustomFeedView = false;
        $sCustomViewType = null;

        if (PHPFOX_IS_AJAX && ($iUserId = $this->request()->get('profile_user_id'))) {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                define('PHPFOX_IS_USER_PROFILE', true);
            }
            $aUser = Phpfox::getService('user')->get($iUserId);

            $this->template()->assign(array(
                    'aUser' => $aUser
                )
            );
        }

        if (PHPFOX_IS_AJAX && $this->request()->get('callback_module_id')) {
            $aCallback = Phpfox::callback($this->request()->get('callback_module_id') . '.getFeedDisplay',
                $this->request()->get('callback_item_id'));
            $this->setParam('aFeedCallback', $aCallback);
        }

        $aFeedCallback = $this->getParam('aFeedCallback', null);

        $bIsProfile = (is_numeric($iUserId) && $iUserId > 0);

        if ($this->request()->get('feed') && $bIsProfile) {
            switch ($this->request()->get('flike')) {
                default:
                    if ($sPlugin = Phpfox_Plugin::get('feed.component_block_display_process_flike')) {
                        eval($sPlugin);
                    }
                    break;
            }
        }

        if (defined('PHPFOX_IS_USER_PROFILE') && !Phpfox::getService('user.privacy')->hasAccess($iUserId, 'feed.view_wall')) {
            return false;
        }

        //Check permission in pages/groups
        if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') &&
            !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, PHPFOX_PAGES_ITEM_TYPE . '.share_updates')
        ) {
            $aFeedCallback['disable_share'] = true;
        }

        //Check permission in Events

        if (defined('PHPFOX_IS_EVENT_VIEW') && !Phpfox::getUserParam('event.can_post_comment_on_event')) {
            $aFeedCallback['disable_share'] = true;
        }

        $iFeedPage = $this->request()->get('page', 0);
        $iLastFeedId = $this->request()->get('last-feed-id', null);
        if ($iLastFeedId) {
            $iLastFeedId = str_replace('js_item_feed_', '', $iLastFeedId);
        }


        if ($this->request()->getInt('status-id')
            || $this->request()->getInt('comment-id')
            || $this->request()->getInt('link-id')
            || $this->request()->getInt('plink-id')
            || $this->request()->getInt('poke-id')
            || $this->request()->getInt('feed')
        ) {
            $bIsCustomFeedView = true;
            if ($this->request()->getInt('status-id')) {
                $sCustomViewType = _p('status_update_iid', array('iId' => $this->request()->getInt('status-id')));
            } elseif ($this->request()->getInt('link-id')) {
                $sCustomViewType = _p('link_iid', array('iId' => $this->request()->getInt('link-id')));
            } elseif ($this->request()->getInt('plink-id')) {
                $sCustomViewType = _p('link_iid', array('iId' => $this->request()->getInt('plink-id')));
            } elseif ($this->request()->getInt('poke-id')) {
                $sCustomViewType = _p('poke_iid', array('iId' => $this->request()->getInt('poke-id')));
            } elseif ($this->request()->getInt('comment-id')) {
                $sCustomViewType = _p('wall_comment_iid', array('iId' => $this->request()->getInt('comment-id')));

                Phpfox::getService('notification.process')->delete('feed_comment_profile',
                    $this->request()->getInt('comment-id'), Phpfox::getUserId());
            } elseif ($this->request()->getInt('feed')) {
                $sCustomViewType = _p('feed');
            }
        }

        if ((!isset($aFeedCallback['item_id']) || $aFeedCallback['item_id'] == 0)) {
            $aFeedCallback['item_id'] = ((int)$this->request()->get('amp;callback_item_id')) > 0 ? $this->request()->get('amp;callback_item_id') : $this->request()->get('callback_item_id');
        }

        $bStreamMode = false;

        $bUseFeedForm = true;
        if (
            (Phpfox_Module::instance()->getFullControllerName() == 'core.index-member')
            || (defined('PHPFOX_CURRENT_TIMELINE_PROFILE') && PHPFOX_CURRENT_TIMELINE_PROFILE == Phpfox::getUserId())
        ) {
            $bUseFeedForm = false;
        }

        if (!Phpfox::isUser() || defined('PHPFOX_IS_PAGES_VIEW') || $sCustomViewType) {
            $bStreamMode = false;
        }

        $bForceReloadOnPage = false;
        $aRows = array();
        if (PHPFOX_IS_AJAX || !$bForceReloadOnPage || $bIsCustomFeedView) {
            $aRows = Phpfox::getService('feed')->callback($aFeedCallback)->get(($bIsProfile > 0 ? $iUserId : null),
                ($this->request()->get('feed') ? $this->request()->get('feed') : null), $iFeedPage, $bStreamMode, true,
                $iLastFeedId);
            if (empty($aRows)) {
                $iFeedPage++;
                $aRows = Phpfox::getService('feed')->callback($aFeedCallback)->get(($bIsProfile > 0 ? $iUserId : null),
                    ($this->request()->get('feed') ? $this->request()->get('feed') : null), $iFeedPage, $bStreamMode,
                    true, $iLastFeedId);
            }
        }

        if (($this->request()->getInt('status-id')
                || $this->request()->getInt('comment-id')
                || $this->request()->getInt('link-id')
                || $this->request()->getInt('poke-id')
                || $this->request()->getInt('feed')
            )
            && isset($aRows[0])
        ) {
            if (isset($aRows[0]['feed_total_like'])) {
                $aRows[0]['feed_view_comment'] = true;
                $this->setParam('aFeed',
                    array_merge(array('feed_display' => 'view', 'total_like' => $aRows[0]['feed_total_like']),
                        $aRows[0]));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('feed.component_block_display_process')) ? eval($sPlugin) : false);

        if ($bIsCustomFeedView && !count($aRows) && $bIsProfile) {
            $aUser = $this->getParam('aUser');

            $this->url()->send($aUser['user_name'], null, _p('the_activity_feed_you_are_looking_for_does_not_exist'));
        }

        $iUserid = ($bIsProfile > 0 ? $iUserId : null);
        $iTotalFeeds = (int)Phpfox::getComponentSetting(($iUserid === null ? Phpfox::getUserId() : $iUserid),
            'feed.feed_display_limit_' . ($iUserid !== null ? 'profile' : 'dashboard'),
            Phpfox::getParam('feed.feed_display_limit'));

        if (PHPFOX_IS_AJAX && (!$iTotalFeeds || $iTotalFeeds == 0)) {
            return false;
        }

        $aUserLocation = Phpfox::getUserBy('location_latlng');
        if (!empty($aUserLocation)) {
            $this->template()->assign(array('aVisitorLocation' => json_decode($aUserLocation, true)));
        }
        $bLoadCheckIn = false;
        if ((!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && $iUserid == Phpfox::getUserId())) && !defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_IS_EVENT_VIEW') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key')) {
            $bLoadCheckIn = true;
        }
        $bLoadTagFriends = false;
        if (Phpfox::getParam('feed.enable_tag_friends') && $this->getParam('allowTagFriends', true))
        {
            $bLoadTagFriends = true;
        }

        $bIsHashTagPop = ($this->request()->get('hashtagpopup') ? true : false);
        if ($bIsHashTagPop) {
            define('PHPFOX_FEED_HASH_POPUP', true);
        }

        $sIsHashTagSearchValue = strip_tags(Phpfox::getService('feed')->getSearchHashtag());

        //Load sponsor feed here
        $iSponsorFeedId = 0;
        $bIsCheckForUpdate = defined('PHPFOX_CHECK_FOR_UPDATE_FEED') ? 1 : 0;
        if (!$sIsHashTagSearchValue && !$bIsCheckForUpdate && !defined('PHPFOX_IS_USER_PROFILE') &&
            !defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_IS_EVENT_VIEW') && Phpfox_Request::instance()->getInt('page') == 0 &&
            Phpfox::isModule('ad') && Phpfox::getParam('ad.multi_ad') && (($iAd = Phpfox::getService('ad')->getSponsoredFeed()) != false)
        ) {
            $iTotalFeeds = (int)Phpfox::getComponentSetting(($iUserId === null ? Phpfox::getUserId() : $iUserId),
                'feed.feed_display_limit_' . ($iUserId !== null ? 'profile' : 'dashboard'),
                Phpfox::getParam('feed.feed_display_limit'));
            $aSponsored = Phpfox::getService('feed')->get(null, $iAd);
            if (isset($aSponsored[0])) {
                $iSponsorFeedId = $aSponsored[0]['feed_id'];
                $aSponsored[0]['sponsored_feed'] = true;
                if (count($aRows) >= $iTotalFeeds) {
                    $aRows = array_splice($aRows, 0, count($aRows) - 1);
                }
                $aRows = array_merge($aSponsored, $aRows);
            }
        }

        $iUserProfileId = (defined('PHPFOX_IS_USER_PROFILE') && isset($iUserid)) ? $iUserid : 0;

        if(Phpfox::isModule('share') and !empty($aRows)){
            $query =  [];
            foreach($aRows as $index=>$aRow){
                $query[] =  Phpfox::getLib('database')
                    ->select( $index .' as row_index, count(*) as total_share')
                    ->from(':feed', 'f')
                    ->where('parent_module_id=\'' . $aRow['type_id'] . '\' AND parent_feed_id=' . (int)$aRow['item_id'])
                    ->execute('');
            }

            $aShareCount = Phpfox::getLib('database')->getSlaveRows(implode(' UNION ALL ', $query));

            foreach($aShareCount as $share){
                $aRows[$share['row_index']]['total_share'] = $share['total_share'];
            }
        }

        $this->template()->assign(array(
                'bUseFeedForm'          => $bUseFeedForm,
                'bStreamMode'           => $bStreamMode,
                'bForceReloadOnPage'    => $bForceReloadOnPage,
                'bHideEnterComment'     => true,
                'aFeeds'                => $aRows,
                'iFeedNextPage'         => ($bForceReloadOnPage ? 0 : ((int)$iFeedPage + 1)),
                'iFeedCurrentPage'      => $iFeedPage,
                'iTotalFeedPages'       => 1,
                'aFeedVals'             => $this->request()->getArray('val'),
                'sCustomViewType'       => $sCustomViewType,
                'aFeedStatusLinks'      => Phpfox::getService('feed')->getShareLinks(),
                'aFeedCallback'         => $aFeedCallback,
                'bIsCustomFeedView'     => $bIsCustomFeedView,
                'sTimelineYear'         => $this->request()->get('year'),
                'sTimelineMonth'        => $this->request()->get('month'),
                'iFeedUserSortOrder'    => Phpfox::getUserBy('feed_sort'),
                'bLoadCheckIn'          => $bLoadCheckIn,
                'bLoadTagFriends' => $bLoadTagFriends,
                'bForceFormOnly'        => $bForceFormOnly,
                'sIsHashTagSearch'      => urlencode($sIsHashTagSearchValue),
                'sIsHashTagSearchValue' => $sIsHashTagSearchValue,
                'bIsHashTagPop'         => $bIsHashTagPop,
                'iSponsorFeedId'        => $iSponsorFeedId,
                'iUserProfileId'        => $iUserProfileId,
                'bForceFlavor' => $this->request()->get('force-flavor')
            )
        );

        return 'block';
    }

    public function clean()
    {
        $this->template()->clean(array(
                'sHeader',
                'aFeeds',
                'sBoxJsId'
            )
        );
    }
}