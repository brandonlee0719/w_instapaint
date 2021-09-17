<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');


class Marketplace extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('marketplace');
    }

    public function getListing($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getlisting')) ? eval($sPlugin) : false);

        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'lik',
                    'lik.type_id = \'marketplace\' AND lik.item_id = l.listing_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::isModule('track')) {
            $sJoinQuery = Phpfox::isUser() ? 'marketplace_track.user_id = ' . Phpfox::getUserBy('user_id') : 'marketplace_track.ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
            $this->database()->select("marketplace_track.item_id AS is_viewed, ")
                ->leftJoin(Phpfox::getT('track'), 'marketplace_track',
                    'marketplace_track.item_id = l.listing_id AND marketplace_track.type_id=\'marketplace\' AND '.$sJoinQuery);
        }
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = l.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        $aListing = $this->database()->select(Phpfox::getUserField() . ', l.*, mcd.category_id, ml.invite_id, ml.visited_id, uf.total_score, uf.total_rating, ua.activity_points, ' . (Phpfox::getParam('core.allow_html') ? 'mt.description_parsed' : 'mt.description') . ' AS description')
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('marketplace_category_data'), 'mcd', 'mcd.listing_id = l.listing_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = l.user_id')
            ->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = l.user_id')
            ->leftJoin(Phpfox::getT('marketplace_invite'), 'ml',
                'ml.listing_id = l.listing_id AND ml.invited_user_id = ' . Phpfox::getUserId())
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!isset($aListing['listing_id'])) {
            return false;
        }
        if (!isset($aListing['is_liked'])) {
            $aListing['is_liked'] = false;
        }
        if (!isset($aListing['is_viewed'])) {
            $aListing['is_viewed'] = 0;
        }
        if (!isset($aListing['is_friend'])) {
            $aListing['is_friend'] = 0;
        }
        if ($aListing['view_id'] == '1') {
            if ($aListing['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('marketplace.can_approve_listings')) {

            } else {
                return false;
            }
        }

        $aListing['categories'] = Phpfox::getService('marketplace.category')->getCategoriesById($aListing['listing_id']);
        $aListing['bookmark_url'] = Phpfox_Url::instance()->permalink('marketplace', $aListing['listing_id'],
            $aListing['title']);

        return $aListing;
    }

    public function getForEdit($iId, $bForce = false)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getforedit')) ? eval($sPlugin) : false);

        $aListing = $this->database()->select('l.*, description')
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('marketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!$aListing) {
            return false;
        }
        $aListing['params'] = [
            'id' => $aListing['listing_id']
        ];
        if ((($aListing['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_edit_own_listing')) || Phpfox::getUserParam('marketplace.can_edit_other_listing')) || ($bForce === true)) {
            $aListing['categories'] = Phpfox::getService('marketplace.category')->getCategoryIds($aListing['listing_id']);

            return $aListing;
        }

        return \Phpfox_Error::display(_p('unable_to_edit_this_listing_dot'));
    }

    public function getInvoice($iId)
    {
        $aInvoice = $this->database()->select('mi.*, m.title, m.user_id AS marketplace_user_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
            ->where('mi.invoice_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        return (isset($aInvoice['invoice_id']) ? $aInvoice : false);
    }

    public function getInvoices($aCond, $bGroupUser = false)
    {
        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->where($aCond)
            ->execute('getSlaveField');

        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $aRows = $this->database()->select('mi.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('marketplace_invoice'), 'mi')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
            ->where($aCond)
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            switch ($aRow['status']) {
                case 'completed':
                    $aRows[$iKey]['status_phrase'] = _p('paid');
                    break;
                case 'cancel':
                    $aRows[$iKey]['status_phrase'] = _p('cancelled');
                    break;
                case 'pending':
                    $aRows[$iKey]['status_phrase'] = _p('pending_payment');
                    break;
                default:
                    $aRows[$iKey]['status_phrase'] = _p('pending_payment');
                    break;
            }
        }

        return array($iCnt, $aRows);
    }

    public function getForProfileBlock($iUserId, $iLimit = 5)
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getforprofileblock')) ? eval($sPlugin) : false);

        return $this->database()->select('m.*')
            ->from($this->_sTable, 'm')
            ->where('m.view_id = 0 AND m.group_id = 0 AND m.user_id = ' . (int)$iUserId)
            ->limit($iLimit)
            ->order('m.time_stamp DESC')
            ->execute('getSlaveRows');
    }

    public function getSponsorListings($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('marketplace_sponsored');

        if (!($sListingIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
            $where = 'm.view_id = 0 AND m.group_id = 0 AND m.is_sponsor = 1 AND s.module_id = \'marketplace\' AND s.is_active = 1';
            if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) {
                $where .= ' AND m.time_stamp >= ' . $iExpireTime;
            }
            $aListingIds = $this->database()->select('m.listing_id')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = m.listing_id')
                ->where($where)
                ->execute('getSlaveRows');

            foreach ($aListingIds as $key => $aId) {
                if ($key != 0) {
                    $sListingIds .= ',' . $aId['listing_id'];
                } else {
                    $sListingIds = $aId['listing_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sListingIds);
            }
        }
        if (empty($sListingIds)) {
            return [];
        }
        $aListingIds = explode(',', $sListingIds);
        shuffle($aListingIds);
        $aListingIds = array_slice($aListingIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));

        $aListing = $this->database()->select('s.*, m.listing_id, m.title, m.currency_id, m.price, m.time_stamp, m.image_path, m.server_id, m.total_like, m.total_view as total_view_listing, '.Phpfox::getUserField())
            ->from($this->_sTable, 'm')
           ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
           ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = m.listing_id AND s.module_id = \'marketplace\'')
           ->where('m.listing_id IN (' . implode(',', $aListingIds) . ')')
           ->limit($iLimit)
           ->execute('getSlaveRows');

        if ($aListing === true || (is_array($aListing) && !count($aListing))) {
            return array();
        }
        if (Phpfox::isModule('ad')) {
            $aListing = Phpfox::getService('ad')->filterSponsor($aListing);
        }
        foreach ($aListing as $key => $aItem) {
            $aListing[$key]['total_view'] = $aItem['total_view_listing'];
        }
        shuffle($aListing);

        return $aListing;
    }

    public function getInvites($iListing, $iType, $iPage = 0, $iPageSize = 8)
    {
        $aInvites = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_invite'))
            ->where('listing_id = ' . (int)$iListing . ' AND visited_id = ' . (int)$iType)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aInvites = $this->database()->select('ei.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('marketplace_invite'), 'ei')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                ->where('ei.listing_id = ' . (int)$iListing . ' AND ei.visited_id = ' . (int)$iType)
                ->limit($iPage, $iPageSize, $iCnt)
                ->order('ei.invite_id DESC')
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aInvites);
    }

    public function getUserListings($iListingId, $iUserId, $iLimit = 4)
    {
        $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
        $sExtraCond = (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND v.time_stamp >= ' . $iExpireTime : '';
        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getuserlistings_count')) ? eval($sPlugin) : false);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId . $sExtraCond)
            ->execute('getSlaveField');

        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getuserlistings_query')) ? eval($sPlugin) : false);

        $aRows = $this->database()->select(Phpfox::getUserField() . (Phpfox::getUserField() ? ',' : '') . 'v.*')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId . $sExtraCond)
            ->limit($iLimit)
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aRows);
    }

    public function getRelatedListings($iCategoryId, $iListingId, $iLimit = 4)
    {
        $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
        $sExtraCond = (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND v.time_stamp >= ' . $iExpireTime : '';


        (($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace_getrelatedlistings_query')) ? eval($sPlugin) : false);

        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->join(Phpfox::getT('marketplace_category_data'), 'mcd', 'mcd.listing_id = v.listing_id')
            ->join(Phpfox::getT('marketplace_category'), 'mc', 'mc.category_id = mcd.category_id')
            ->where('v.listing_id <>' . $iListingId . ' AND mcd.category_id = ' . $iCategoryId . ' AND v.view_id = 0 ' . $sExtraCond)
            ->limit($iLimit)
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');
        return $aRows;
    }

    public function isAlreadyInvited($iItemId, $aFriends)
    {
        if ((int)$iItemId === 0) {
            return false;
        }

        if (is_array($aFriends)) {
            if (!count($aFriends)) {
                return false;
            }

            $sIds = [];
            foreach ($aFriends as $aFriend) {
                if (!isset($aFriend['user_id'])) {
                    continue;
                }

                $sIds[] = $aFriend['user_id'];
            }

            $aInvites = $this->database()->select('invite_id, visited_id, invited_user_id')
                ->from(Phpfox::getT('marketplace_invite'))
                ->where('listing_id = ' . (int)$iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                ->execute('getSlaveRows');

            $aCache = array();
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['visited_id'] ? _p('visted') : _p('invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }

    public function getFeatured($iLimit = 4, $iCacheTime = 4)
    {
        $sCacheId = $this->cache()->set('marketplace_featured');

        if (!($sListingIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $iExpireTime = (PHPFOX_TIME - (setting('marketplace.days_to_expire_listing') * 86400));
            $sExtraCond = (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND m.time_stamp >= ' . $iExpireTime : '';
            $aListingIds = $this->database()->select('m.listing_id')
                ->from(Phpfox::getT('marketplace'), 'm')
                ->where('m.view_id = 0 AND m.is_featured = 1' . $sExtraCond)
                ->execute('getSlaveRows');
            foreach ($aListingIds as $key => $aId) {
                if ($key != 0) {
                    $sListingIds .= ',' . $aId['listing_id'];
                } else {
                    $sListingIds = $aId['listing_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sListingIds);
            }
        }

        if (empty($sListingIds)) {
            return array();
        }
        $aListingIds = explode(',', $sListingIds);
        shuffle($aListingIds);
        $aListingIds = array_slice($aListingIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));

        $aRows = $this->database()->select('m.*')
            ->from(Phpfox::getT('marketplace'), 'm')
            ->where('m.listing_id IN (' . implode(',', $aListingIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (!is_array($aRows) || !count($aRows)) {
            return array();
        }

        shuffle($aRows);

        return $aRows;
    }

    public function getImages($iId, $iLimit = null, $exclude_image_path = null)
    {
        $select = $this->database()->select('image_id, image_path, server_id')
            ->from(Phpfox::getT('marketplace_image'))
            ->order('ordering ASC')
            ->limit($iLimit);

        if (!empty($exclude_image_path)) {
            $exclude_image_path = $this->database()->escape($exclude_image_path);
            $select->where('listing_id = ' . (int)$iId . " AND image_path <> '$exclude_image_path'");
        } else {
            $select->where('listing_id = ' . (int)$iId);
        }

        return $select->execute('getSlaveRows');
    }

    /**
     * @param  int $iId
     *
     * @return int
     */
    public function countImages($iId)
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('marketplace_image'))
            ->where('listing_id = ' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getSlaveField');
    }

    public function getUserInvites()
    {
        $iCnt = $this->getTotalInvites();

        $aRows = $this->database()->select('m.*')
            ->from(Phpfox::getT('marketplace_invite'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->where('mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId())
            ->limit(5)
            ->execute('getSlaveRows');

        return array($iCnt, $aRows);
    }

    public function getTotalInvites()
    {
        static $iCnt = null;

        if ($iCnt !== null) {
            return $iCnt;
        }

        $iCnt = (int)$this->database()->select('COUNT(m.listing_id)')
            ->from(Phpfox::getT('marketplace_invite'), 'mi')
            ->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id AND m.view_id = 0')
            ->where('mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');

        return $iCnt;
    }

    public function getInfoForAction($aItem)
    {
        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        }
        $aRow = $this->database()->select('m.listing_id, m.title, m.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('marketplace'), 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.listing_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        $aRow['link'] = Phpfox_Url::instance()->permalink('forum.thread', $aRow['listing_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * @param $aRow
     */
    public function getPermissions(&$aRow)
    {
        $aRow['canEdit'] = (($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_edit_own_listing')) || Phpfox::getUserParam('marketplace.can_edit_other_listing'));
        $aRow['canInvite'] = $aRow['canEdit'] && $aRow['view_id'] == 0;
        $aRow['canSponsorInFeed'] = $aRow['view_id'] == 0 && $aRow['user_id'] == Phpfox::getUserId() && (Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && (Phpfox::getService('feed')->canSponsoredInFeed('marketplace',
                $aRow['listing_id'])));
        $aRow['iSponsorInFeedId'] = Phpfox::getService('feed')->canSponsoredInFeed('marketplace', $aRow['listing_id']);
        $aRow['canDelete'] = ($aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_delete_own_listing')) || Phpfox::getUserParam('marketplace.can_delete_other_listings');
        $aRow['canSponsor'] = $aRow['view_id'] == 0 && (Phpfox::isModule('ad') && Phpfox::getUserParam('marketplace.can_sponsor_marketplace'));
        $aRow['canPurchaseSponsor'] = $aRow['view_id'] == 0 && (Phpfox::isModule('ad') && $aRow['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_purchase_sponsor'));
        $aRow['canApprove'] = (Phpfox::getUserParam('marketplace.can_approve_listings') && $aRow['view_id'] == 1);
        $aRow['canFeature'] = (Phpfox::getUserParam('marketplace.can_feature_listings') && $aRow['view_id'] == 0);
        $aRow['canContactSeller'] = $this->_canContactSeller($aRow);
        $aRow['hasPermission'] = ($aRow['canEdit'] || $aRow['canDelete'] || $aRow['canSponsor'] || $aRow['canApprove'] || $aRow['canFeature'] || $aRow['canPurchaseSponsor'] || $aRow['canSponsorInFeed']);
    }

    private function _canContactSeller($aRow)
    {
        if (Phpfox::getParam('mail.spam_check_messages') && Phpfox::isSpammer()) {
            return false;
        }
        if (Phpfox::getService('mail')->canMessageUser($aRow['user_id']) == false) {
            return false;
        }
        return true;
    }
    public function buildSectionMenu()
    {
        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $sInviteTotal = '';
            if (Phpfox::isUser() && ($iTotalInvites = $this->getTotalInvites())) {
                $sInviteTotal = '<span class="invited">' . $iTotalInvites . '</span>';
            }
            $iMyTotal = $this->getMyTotal();
            $aFilterMenu = array(
                _p('all_listings') => '',
                _p('my_listings') . ($iMyTotal ? '<span class="my count-item">' . ($iMyTotal > 99 ? '99+' : $iMyTotal) . '</span>' : '') => 'my',
                _p('listing_invites') . ($sInviteTotal ? '<span class="count-item">' . ($sInviteTotal > 99 ? '99+' : $sInviteTotal) . '</span>' : '') => 'invites',
                _p('invoices') => 'marketplace.invoice'
            );

            if (Phpfox::getUserParam('marketplace.can_view_expired')) {
                $aFilterMenu[_p('expired')] = 'expired';
            }
            if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community')) {
                $aFilterMenu[_p('friends_listings')] = 'friend';
            }

            if (Phpfox::getUserParam('marketplace.can_approve_listings')) {
                $iPendingTotal = $this->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_listings') . '<span id="marketplace_pending" class="pending count-item">' . ($iPendingTotal > 99 ? '99+' : $iPendingTotal) . '</span>'] = 'pending';
                }
            }
        }
        \Phpfox_Template::instance()->buildSectionMenu('marketplace', $aFilterMenu);
    }

    public function getMyTotal()
    {
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)Phpfox::getUserId())
            ->execute('getSlaveField');
    }

    public function getPendingTotal()
    {
        $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
        $sExtraCond = (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) ? ' AND time_stamp >= ' . $iExpireTime : '';
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('view_id = 1' . $sExtraCond)
            ->execute('getSlaveField');
    }

    public function getUploadParams($aParams = null)
    {
        if (isset($aParams['id'])) {
            $iTotalImage = Phpfox::getService('marketplace')->countImages($aParams['id']);
            $iRemainImage = Phpfox::getUserParam('marketplace.total_photo_upload_limit') - $iTotalImage;
        }
        else {
            $iRemainImage = Phpfox::getUserParam('marketplace.total_photo_upload_limit');
        }
        $iMaxFileSize = Phpfox::getUserParam('marketplace.max_upload_size_listing');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.marketplace.dropzoneOnSending',
            'success' => '$Core.marketplace.dropzoneOnSuccess',
            'queuecomplete' => '$Core.marketplace.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('marketplace.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('marketplace.dir_image'),
            'upload_path' => Phpfox::getParam('marketplace.url_image'),
            'update_space' => true,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'on_remove' => 'marketplace.deleteImage',
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number',['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => Phpfox::getParam('marketplace.thumbnail_sizes')
        ];
    }
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('marketplace.service_marketplace__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
