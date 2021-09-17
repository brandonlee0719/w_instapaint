<?php

namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Pages_Pages;
use Phpfox_Plugin;
use Phpfox_Template;
use Phpfox_Url;

/**
 * Class Groups
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Groups extends Phpfox_Pages_Pages
{
    private $_aPhotoPicSizes = [50, 120, 200, 500, 1024];

    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }

    public function getUrl($iPageId, $sTitle = null, $sVanityUrl = null, $bIsGroup = false)
    {
        if ($sTitle === null && $sVanityUrl === null) {
            $aPage = $this->getPage($iPageId);
            $sVanityUrl = $aPage['vanity_url'];
        }

        if (!empty($sVanityUrl)) {
            return Phpfox_Url::instance()->makeUrl($sVanityUrl);
        }

        return Phpfox_Url::instance()->makeUrl('groups', $iPageId);
    }

    /**
     * @param int|array $iPage
     * @param string $sPerm
     *
     * @return bool
     */
    public function hasPerm($iPage = null, $sPerm)
    {
        if (Phpfox::isAdmin()) {
            return true;
        }
        if (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getUserParam('core.can_view_private_items')) {
            return true;
        }

        if (defined('PHPFOX_POSTING_AS_PAGE')) {
            return true;
        }


        if (is_array($iPage) && isset($iPage['page_id'])) {
            $aPage = $iPage;
        } else {
            $aPage = $this->getPage($iPage);
        }
        $aPerms = \Phpfox::getService('groups')->getPermsForPage($aPage['page_id']);
        if (isset($aPerms[$sPerm])) {
            switch ((int)$aPerms[$sPerm]) {
                case 1:
                    if (!$this->isMember($aPage['page_id'])) {
                        return false;
                    }
                    break;
                case 2:
                    if (!$this->isAdmin($aPage['page_id'])) {
                        return false;
                    }
                    break;
            }
        }
        //If don't set in Permission list, Use groups permission
        if ($aPage['reg_method'] == 0 || $this->isMember($aPage['page_id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getCountConvertibleGroups()
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(':pages', 'p')
            ->join(':pages_category', 'pc', 'p.category_id=pc.category_id')
            ->where('pc.page_type=1')
            ->execute('getSlaveField');

        return $iCnt;
    }

    public function convertOldGroups()
    {
        //each time run in 300 seconds or 1000 groups
        $start = time();

        //Map old groups Category to new
        $aCategories = $this->database()->select('*')
            ->from(':pages_category')
            ->where('page_type=1')
            ->execute('getRows');
        foreach ($aCategories as $aCategory) {
            $aTypeInsert = [
                'is_active' => 1,
                'item_type' => 1,//1 mean groups
                'name' => $aCategory['name'],
                'time_stamp' => PHPFOX_TIME,
                'ordering' => $aCategory['ordering'],
            ];
            $this->database()->insert(':pages_type', $aTypeInsert);
        }
        //Get 1000 old groups
        $aOldGroups = $this->database()->select('p.page_id, p.category_id')
            ->from(':pages', 'p')
            ->join(':pages_category', 'pc', 'p.category_id=pc.category_id')
            ->where('pc.page_type=1')
            ->limit(1000)
            ->execute('getSlaveRows');
        $group_type_id = $this->database()->select('type_id')
            ->from(':pages_type')
            ->where('item_type=1')
            ->execute('getSlaveField');
        foreach ($aOldGroups as $aGroup) {
            //Get new groups type
            $new_groups_type_id = $this->database()->select('pt.type_id')
                ->from(':pages_type', 'pt')
                ->join(':pages_category', 'pc', 'pc.name=pt.name')
                ->where('pc.category_id=' . (int)$aGroup['category_id'] . ' AND pt.item_type=1')
                ->execute('getSlaveField');
            $group_type_id = ($new_groups_type_id > 0) ? $new_groups_type_id : $group_type_id;
            $this->database()->update(':pages', [
                'type_id' => $group_type_id,
                'category_id' => 0,//We do not have default groups category
                'item_type' => 1
            ], 'page_id=' . (int)$aGroup['page_id']);
            //Update blog data
            $this->database()->update(':blog', [
                'module_id' => 'groups'
            ], 'item_id=' . (int)$aGroup['page_id']);

            //Update event data
            $this->database()->update(':event', [
                'module_id' => 'groups'
            ], 'item_id=' . (int)$aGroup['page_id']);

            //Forum: do nothing

            //Update music album
            $this->database()->update(':music_album', [
                'module_id' => 'groups'
            ], 'item_id=' . (int)$aGroup['page_id']);
            //Update music song
            $this->database()->update(':music_song', [
                'module_id' => 'groups'
            ], 'item_id=' . (int)$aGroup['page_id']);

            //Update photo
            $this->database()->update(':photo', [
                'module_id' => 'groups'
            ], 'group_id=' . (int)$aGroup['page_id']);
            //Update photo album
            $this->database()->update(':photo_album', [
                'module_id' => 'groups'
            ], 'group_id=' . (int)$aGroup['page_id']);

            //Update groups comment
            $this->database()->update(':pages_feed', [
                'type_id' => 'groups_comment'
            ], 'type_id="pages_comment" AND parent_user_id=' . (int)$aGroup['page_id']);

            //Update comments on groups
            $this->database()->update(':comment', [
                'type_id' => 'groups'
            ], 'type_id="pages" AND item_id=' . (int)$aGroup['page_id']);

            //Update likes on groups
            db()->update(Phpfox::getT('like'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'],
                'type_id LIKE \'pages%\' AND item_id=' . (int)$aGroup['page_id'], false);
            //Video not yet integrate with pages on 4.2.2

            //Update link data
            $this->database()->update(':link', [
                'module_id' => 'groups'
            ], 'item_id=' . (int)$aGroup['page_id']);

            //Update Home Feed
            db()->update(Phpfox::getT('feed'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'],
                'type_id LIKE \'pages%\' AND item_id=' . (int)$aGroup['page_id'], false);

            //Update Notification
            db()->update(Phpfox::getT('notification'), ['type_id' => 'REPLACE(type_id, \'pages\', \'groups\')'],
                'type_id LIKE \'pages%\' AND item_id=' . (int)$aGroup['page_id'], false);
            //----------------------//
            //End process convert
            $end = time();
            if (($end - $start) >= 300) {
                break;
            }
        }
    }

    public function getMenu($aPage)
    {
        $aMenus = [
            [
                'phrase' => $this->getFacade()->getPhrase('home'),
                'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']) . (empty($aPage['landing_page']) ? '' : 'wall/'),
                'icon' => 'misc/comment.png',
                'landing' => 'home'
            ],
            [
                'phrase' => $this->getFacade()->getPhrase('members'),
                'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']) . 'members',
                'icon' => 'misc/comment.png',
                'landing' => 'members'
            ]
        ];

        if ($this->isAdmin($aPage)) {
            $iTotalPendingMembers = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_signup'))
                ->where('page_id = ' . (int)$aPage['page_id'])
                ->execute('getSlaveField');

            if ($iTotalPendingMembers > 0) {
                Phpfox_Template::instance()->assign('aSubPagesMenus', [
                    [
                        'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                                $aPage['vanity_url']) . 'members#pending',
                        'title' => $this->getFacade()->getPhrase('pending_memberships') . '<span class="pending">&nbsp;(' . $iTotalPendingMembers . ')</span>'
                    ]
                ]);
            }
        }

        switch ($this->getFacade()->getItemType()) {
            case 'pages':
                $aModuleCalls = Phpfox::massCallback('getPageMenu', $aPage);
                break;

            case 'groups':
                $aModuleCalls = Phpfox::massCallback('getGroupMenu', $aPage);
                break;

            default:
                $aModuleCalls = [];
        }

        foreach ($aModuleCalls as $sModule => $aModuleCall) {
            if (!is_array($aModuleCall)) {
                continue;
            }
            if ($aIntegrate = storage()->get($this->getFacade()->getItemType() . '_integrate')) {
                $aIntegrate = (array)$aIntegrate->value;
                if (array_key_exists($sModule, $aIntegrate) && !$aIntegrate[$sModule]) {
                    continue;
                }
            }
            $aMenus[] = $aModuleCall[0];
        }

        if (count($this->_aWidgetMenus)) {
            $aMenus = array_merge($aMenus, $this->_aWidgetMenus);
        }

        foreach ($aMenus as &$aMenu) {
            $aMenu['menu_icon'] = materialParseIcon($aMenu['landing']);
        }

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getmenu')) {
            eval($sPlugin);
        }

        return $aMenus;
    }

    /**
     * Get number of items in a category
     * @param $iCategoryId
     * @param $bIsSub
     * @param $iItemType
     * @param int $iUserId
     * @param bool $bGetCount
     * @param string $sView
     * @return array|int|string
     */
    public function getItemsByCategory($iCategoryId, $bIsSub, $iItemType, $iUserId = 0, $bGetCount = false, $sView = '')
    {
        $aConds = [
            'pages.item_type' => $iItemType,
            'pages.view_id' => 0
        ];

        if ($bIsSub) {
            $aConds['pages.category_id'] = $iCategoryId;
        } else {
            $aConds['pages.type_id'] = $iCategoryId;
        }
        
        switch ($sView) {
            case '':
                $aConds['pages.view_id'] = 0;

                break;
            case 'my':
                $aConds['pages.user_id'] = $iUserId;

                break;
            case 'friend':
                $aFriends = Phpfox::getService('friend')->getFromCache();
                $sFriendsList = implode(',', array_column($aFriends, 'user_id'));
                $aConds['pages.user_id'] = [
                    'in' => $sFriendsList
                ];

                break;
            case 'pending':
                $aConds['pages.view_id'] = 1;

                break;
            default:
                break;
        }

        $extra_conditions = '';
        if (($iUserId != Phpfox::getUserId() || $iUserId === null) && Phpfox::hasCallback(Phpfox::getService('groups.facade')->getItemType(),
                'getExtraBrowseConditions')
        ) {
            $extra_conditions .= Phpfox::callback(Phpfox::getService('groups.facade')->getItemType() . '.getExtraBrowseConditions',
                'pages');
        }

        Phpfox::getService('privacy')->buildPrivacy(
            array(
                'module_id' => Phpfox::getService('groups.facade')->getItemType(),
                'alias' => 'pages',
                'field' => 'page_id',
                'table' => Phpfox::getT('pages'),
                'service' => Phpfox::getService('groups.facade')->getItemType() . '.browse'
            ), 'pages.time_stamp DESC', 0, null, $extra_conditions, false
        );

        $this->database()->unionFrom('pages');

        if ($bGetCount) {
            return db()->select('COUNT(*)')
                ->where($aConds)
                ->executeField();
        } else {
            return db()->select('*')
                ->where($aConds)
                ->executeRows();
        }
    }

    /**
     * check if current user have one of 3 permissions: edit all, delete all, approve
     * @return bool
     */
    public function canModerate()
    {
        $oGroupsFacade = Phpfox::getService('groups.facade');

        return $oGroupsFacade->getUserParam('can_edit_all_pages') || $oGroupsFacade->getUserParam('can_delete_all_pages') || $oGroupsFacade->getUserParam('can_approve_pages');
    }

    public function getActionsPermission(&$aPage, $sView = '')
    {
        $aPage['bCanApprove'] = $sView == 'pending' && $aPage['view_id'] == 1 && Phpfox::getUserParam('groups.can_approve_groups');
        $aPage['bCanEdit'] = Phpfox::getService('groups')->isAdmin($aPage) || Phpfox::getUserParam('groups.can_edit_all_groups');
        $aPage['bCanDelete'] = Phpfox::getUserId() == $aPage['user_id'] || Phpfox::getUserParam('groups.can_delete_all_groups');
        $aPage['bShowItemActions'] = $aPage['bCanApprove'] || $aPage['bCanEdit'] || $aPage['bCanDelete'];
    }

    public function getForView($mId)
    {
        if ($this->_aPage !== null) {
            $mId = $this->_aPage['page_id'];
        }

        $pageUserId = Phpfox::getUserId();

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = p.user_id AND f.friend_user_id = " . $pageUserId);
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.item_id = p.page_id AND l.user_id = ' . $pageUserId);
        }

        $this->_aRow = $this->database()->select('p.*, u.user_image as image_path, p.image_path as pages_image_path, u.user_id as page_user_id, p.use_timeline, pc.claim_id, pu.vanity_url, pg.name AS category_name, p_type.type_id as parent_category_id, pg.page_type, pt.text, pt.text_parsed, u.full_name, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, p_type.name AS parent_category_name, ' . Phpfox::getUserField('u2',
                'owner_'))
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
            ->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pg.type_id')
            ->leftJoin(Phpfox::getT('theme_style'), 'ts', 'ts.style_id = p.designer_style_id')
            ->leftJoin(Phpfox::getT('theme'), 't', 't.theme_id = ts.theme_id')
            ->leftJoin(Phpfox::getT('pages_claim'), 'pc',
                'pc.page_id = p.page_id AND pc.user_id = ' . Phpfox::getUserId())
            ->where('p.page_id = ' . (int)$mId . ' AND p.item_type = ' . $this->getFacade()->getItemTypeId())
            ->execute('getSlaveRow');

        if (!isset($this->_aRow['page_id'])) {
            return false;
        }

        $this->_aRow['is_page'] = true;
        $this->_aRow['is_admin'] = $this->isAdmin($this->_aRow);
        $this->_aRow['link'] = $this->getFacade()->getItems()->getUrl($this->_aRow['page_id'], $this->_aRow['title'],
            $this->_aRow['vanity_url']);

        if (($this->_aRow['page_type'] == '1' || $this->_aRow['item_type'] != '0') && $this->_aRow['reg_method'] == '1') {
            $this->_aRow['is_reg'] = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_signup'))
                ->where('page_id = ' . (int)$this->_aRow['page_id'] . ' AND user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');
        }

        if ($this->_aRow['reg_method'] == '2' && Phpfox::isUser()) {
            $this->_aRow['is_invited'] = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_invite'))
                ->where('page_id = ' . (int)$this->_aRow['page_id'] . ' AND invited_user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');

            if (!$this->_aRow['is_invited']) {
                unset($this->_aRow['is_invited']);
            }
        }

        if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id')) {
            $this->_aRow['is_liked'] = true;
        }

        // Issue with like/join button
        // Still not defined
        if (!isset($this->_aRow['is_liked'])) {
            // make it false: not liked or joined yet
            $this->_aRow['is_liked'] = false;
        }

        if ($this->_aRow['app_id']) {
            if ($this->_aRow['aApp'] = Phpfox::getService('apps')->getForPage($this->_aRow['app_id'])) {
                $this->_aRow['is_app'] = true;
                $this->_aRow['title'] = $this->_aRow['aApp']['app_title'];
                $this->_aRow['category_name'] = 'App';
            }
        } else {
            $this->_aRow['is_app'] = false;
        }

        $oUrl = Phpfox_Url::instance();
        if ($this->_aRow['type_id']) {
            $aType = $this->getFacade()->getType()->getById($this->_aRow['type_id']);
        }
        if (!empty($aType)) {
            $this->_aRow['parent_category_name'] = $aType['name'];
        }
        if ($this->_aRow['parent_category_name']) {
            $this->_aRow['parent_category_link'] = $oUrl->makeUrl('groups.category.' . $this->_aRow['type_id'] . '.' . $oUrl->cleanTitle(_p($this->_aRow['parent_category_name'])));
        }
        if ($this->_aRow['category_name']) {
            $this->_aRow['category_link'] = $oUrl->makeUrl('groups.category.' . $this->_aRow['parent_category_id'] . '.' . $oUrl->cleanTitle(_p($this->_aRow['category_name'])));
        }

        return $this->_aRow;
    }

    /**
     * Get widget by ordering ASC
     * @param $iPageId
     * @param bool $bIsBlock
     * @return array
     */
    public function getWidgetsOrdering($iPageId, $bIsBlock = true)
    {
        return db()->select('*')->from(':pages_widget')->where([
            'page_id' => $iPageId,
            'is_block' => intval($bIsBlock)
        ])->order('ordering ASC')->executeRows();
    }

    /**
     * Update widget order
     * @param $iWidgetId
     * @param $iOrder
     */
    public function updateWidgetOrder($iWidgetId, $iOrder)
    {
        db()->update(':pages_widget', ['ordering' => $iOrder], ['widget_id' => $iWidgetId]);
    }

    /**
     * Build widgets
     * @param $iId
     */
    public function buildWidgets($iId)
    {
        if (!$this->getFacade()->getItems()->hasPerm($iId,
            $this->getFacade()->getItemType() . '.view_browse_widgets')) {
            return;
        }

        $aWidgets = $this->database()->select('pw.*, pwt.text_parsed AS text')
            ->from(Phpfox::getT('pages_widget'), 'pw')
            ->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
            ->where('pw.page_id = ' . (int)$iId)
            ->order('pw.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aWidgets as $aWidget) {
            $this->_aWidgetEdit[] = array(
                'widget_id' => $aWidget['widget_id'],
                'title' => $aWidget['title'],
                'is_block' => $aWidget['is_block'],
                'menu_title' => $aWidget['menu_title'],
                'url_title' => $aWidget['url_title']
            );

            if (!$aWidget['is_block']) {
                $this->_aWidgetMenus[] = array(
                    'phrase' => $aWidget['menu_title'],
                    'url' => $this->getUrl($aWidget['page_id'], $this->_aRow['title'],
                            $this->_aRow['vanity_url']) . $aWidget['url_title'] . '/',
                    'landing' => $aWidget['url_title'],
                    'icon_pass' => (empty($aWidget['image_path']) ? false : true),
                    'icon' => $aWidget['image_path'],
                    'icon_server' => $aWidget['image_server_id']
                );
            }

            $this->_aWidgetUrl[$aWidget['url_title']] = $aWidget['widget_id'];

            if ($aWidget['is_block']) {
                $this->_aWidgetBlocks[] = $aWidget;
            } else {
                $this->_aWidgets[$aWidget['url_title']] = $aWidget;
            }
        }
    }

    public function getMembers($iGroupId, $iLimit = 20, $iPage = 1, $sSearch = '')
    {
        if (!Phpfox::isModule('like')) {
            return false;
        }
        $aWhere = [
            'l.type_id' => 'groups',
            'l.item_id' => intval($iGroupId)
        ];

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('like'), 'l')
            ->where($aWhere)
            ->execute('getSlaveField');

        if ($sSearch) {
            $aWhere['u.full_name'] = ['LIKE' => "%$sSearch%"];
        }

        $aLikes = $this->database()->select('uf.total_friend, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('like'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
            ->where($aWhere)
            ->order('u.full_name ASC')
            ->group('u.user_id')
            ->limit($iPage, $iLimit)
            ->executeRows();

        return [$iCnt, $aLikes];
    }

    public function getPageAdmins($iGroupId = null, $iPage = 1, $iLimit = null, $sSearch = null)
    {
        $this->getForView($iGroupId);
        $aOwnerAdmin = $aPageAdmins = [];
        if ($iPage == 1) {
            foreach ($this->_aRow as $sKey => $mValue) {
                if (substr($sKey, 0, 6) == 'owner_') {
                    $aOwnerAdmin[0][str_replace('owner_', '', $sKey)] = $mValue;
                }
            }
            $iLimit && $iLimit--;
        }

        if ($sSearch && !empty($aOwnerAdmin) && stristr($aOwnerAdmin[0]['full_name'], $sSearch) === false) {
            $aOwnerAdmin = [];
        }

        if ($iLimit) {
            $this->database()->limit($iPage - 1, $iLimit);
        }

        $aWhere = ['pa.page_id' => $iGroupId];
        if ($sSearch) {
            $aWhere['u.full_name'] = ['LIKE' => "%$sSearch%"];
        }
        if (!empty($aOwnerAdmin)) {
            $aWhere[] = 'AND pa.user_id != ' . $aOwnerAdmin[0]['user_id'];
        }

        $aPageAdmins = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('pages_admin'), 'pa')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where($aWhere)
            ->executeRows();

        return array_merge($aOwnerAdmin, $aPageAdmins);
    }

    public function getGroupAdminsCount($iGroupId)
    {
        $aGroup = $this->getPage($iGroupId);

        return db()->select('COUNT(*)')->from(':pages_admin')->where([
            'page_id' => $iGroupId,
            ' AND user_id != ' . $aGroup['user_id']
        ])->executeField() + 1;
    }

    public function getPendingUsers($iGroupId, $bIsCount = false, $iPage = 1, $iLimit = null, $sSearch = null)
    {
        $this->database()
            ->from(Phpfox::getT('pages_signup'), 'ps')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
            ->where(array_merge([
                'ps.page_id' => intval($iGroupId)
            ], $sSearch ? ['u.full_name' => ['LIKE' => "%$sSearch%"]] : []));

        if ($bIsCount) {
            return $this->database()->select('count(*)')->executeField();
        } else {
            if ($iLimit) {
                $this->database()->limit($iPage, $iLimit);
            }

            return $this->database()->select('ps.*, ' . Phpfox::getUserField())->executeRows();
        }
    }

    /**
     * Move items to another category
     * @param $iOldCategoryId
     * @param $iNewCategoryId
     * @param $bOldIsSub , true if old category is sub category
     * @param $bNewIsSub , true if new category is sub category
     * @param $iItemType
     */
    public function moveItemsToAnotherCategory($iOldCategoryId, $iNewCategoryId, $bOldIsSub, $bNewIsSub, $iItemType)
    {
        $aItems = Phpfox::getService('groups')->getItemsByCategory($iOldCategoryId, $bOldIsSub, $iItemType);
        if ($bNewIsSub) {
            // get type id
            $iTypeId = Phpfox::getService('groups.category')->getById($iNewCategoryId)['type_id'];
            $aUpdates = [
                'type_id' => $iTypeId,
                'category_id' => $iNewCategoryId
            ];
        } else {
            $aUpdates = [
                'type_id' => $iNewCategoryId
            ];
        }
        foreach ($aItems as $aItem) {
            db()->update(Phpfox::getT('pages'), $aUpdates, 'page_id = ' . $aItem['page_id']);
        }
    }

    /**
     * Get page for profile index
     * @param $iUserId
     * @param int $iLimit
     * @param bool $bNoCount
     * @param string $sConds
     * @return array
     */
    public function getForProfile($iUserId, $iLimit = 0, $bNoCount = false, $sConds = '')
    {
        $iCnt = 0;
        if ($bNoCount == false) {
            $iCnt = $this->database()->select('p.*, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('like'), 'l')
                ->join(Phpfox::getT('pages'), 'p',
                    'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
                ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int)$iUserId . $sConds)
                ->group('p.page_id', true)// fixes displaying duplicate pages if there are duplicate likes
                ->order('l.time_stamp DESC')
                ->execute('getSlaveRows');
            $iCnt = count($iCnt);
        }

        if ($iLimit) {
            $this->database()->limit($iLimit);
        }

        $aPages = $this->database()->select(Phpfox::getUserField() . ', p.*, pt.name as type_name, pc.name as category_name, ph.destination as cover_image_path, ph.server_id as cover_image_server_id, pu.vanity_url, u.server_id, ptxt.text_parsed')
            ->from(Phpfox::getT('like'), 'l')
            ->join(Phpfox::getT('pages'), 'p',
                'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->leftJoin(':pages_type', 'pt', 'p.type_id = pt.type_id')
            ->leftJoin(':pages_text', 'ptxt', 'ptxt.page_id= p.page_id')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int)$iUserId . $sConds)
            ->group('p.page_id', true)// fixes displaying duplicate pages if there are duplicate likes
            ->order('l.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aPages as $iKey => &$aPage) {
            $aPage['is_app'] = false;
            $aPage['is_user_page'] = true;
            $aPage['user_image'] = sprintf($aPage['image_path'], '_50');
            $aPage['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
            $this->getActionsPermission($aPage);
        }

        return array($iCnt, $aPages);
    }

    /**
     * Get group info
     * @param $iGroupId
     * @param bool $bGetParsed
     * @return string
     */
    public function getInfo($iGroupId, $bGetParsed = false)
    {
        if ($bGetParsed) {
            return db()->select('text_parsed')->from(':pages_text')->where(['page_id' => $iGroupId])->executeField();
        }

        return db()->select('text')->from(':pages_text')->where(['page_id' => $iGroupId])->executeField();
    }

    /**
     * Get upload photo params, support dropzone
     *
     * @return array
     */
    public function getUploadPhotoParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('groups.pf_group_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('pages.dir_image'),
            'upload_path' => Phpfox::getParam('pages.url_image'),
            'thumbnail_sizes' => $this->getPhotoPicSizes(),
        ];
    }

    /**
     * Get photo sizes
     *
     * @return array
     */
    public function getPhotoPicSizes()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.service_groups_getphotopicsizes')) ? eval($sPlugin) : false);

        return $this->_aPhotoPicSizes;
    }

    /**
     * @param $iGroupId
     * @param null $iUserId
     * @return bool
     */
    public function joinGroupRequested($iGroupId, $iUserId = null)
    {
        $iUserId === null && $iUserId = Phpfox::getUserId();

        return !!db()->select('*')->from(':pages_signup')->where([
            'page_id' => $iGroupId,
            'user_id' => $iUserId
        ])->executeField();
    }

    /**
     * Get pages in the same category
     * @param $iPageid
     * @param int $iLimit
     * @return array|bool
     */
    public function getSameCategoryPages($iPageid, $iLimit = 0)
    {
        $aPage = db()->select('type_id, category_id')->from($this->_sTable)->where(['page_id' => $iPageid])->executeRow();
        if (!$aPage) {
            return false;
        }

        $iPageid && db()->limit($iLimit);

        return db()->select('p.*, pc.name as category, pu.vanity_url, u.*')
            ->from($this->_sTable, 'p')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(':pages_url', 'pu', 'p.page_id = pu.page_id')
            ->leftJoin(':user', 'u', 'u.profile_page_id = p.page_id')
            ->where("p.page_id != $iPageid AND p.type_id = $aPage[type_id]")
            ->order('rand()')
            ->executeRows();
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('p.*, pu.vanity_url, pt.text, pc.page_type, p_type.item_type')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pc.type_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->where('p.page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_edit'));
        }

        if (!$this->isAdmin($aRow) && !Phpfox::getUserParam('groups.can_edit_all_groups')) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('you_are_unable_to_edit_this_page'));
        }

        $this->_aRow = $aRow;

        $this->getFacade()->getItems()->buildWidgets($aRow['page_id']);

        $aRow['admins'] = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('pages_admin'), 'pa')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where('pa.page_id = ' . (int)$aRow['page_id'])
            ->execute('getSlaveRows');

        $aRow['admin_ids'] = [];
        foreach ($aRow['admins'] as $aAdmin) {
            $aRow['admin_ids'][] = $aAdmin['user_id'];
        }

        $aRow['admin_ids'] = json_encode($aRow['admin_ids']);

        $aMenus = $this->getMenu($aRow);
        foreach ($aMenus as $iKey => $aMenu) {
            $aMenus[$iKey]['is_selected'] = false;
        }
        if (!empty($aRow['landing_page'])) {
            foreach ($aMenus as $iKey => $aMenu) {
                if ($aMenu['landing'] == $aRow['landing_page']) {
                    $aMenus[$iKey]['is_selected'] = true;
                }
            }
        }

        $aRow['landing_pages'] = $aMenus;

        if ($aRow['app_id']) {
            if ($aRow['aApp'] = Phpfox::getService('apps')->getForPage($aRow['app_id'])) {
                $aRow['is_app'] = true;
                $aRow['title'] = $aRow['aApp']['app_title'];
            }
        } else {
            $aRow['is_app'] = false;
        }
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getforedit_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        defined('PHPFOX_PAGES_EDIT_ID') or define('PHPFOX_PAGES_EDIT_ID', $aRow['page_id']);
        $aRow['location']['name'] = $aRow['location_name'];
        $aRow['image_path_200'] = Phpfox::getLib('image.helper')->display([
            'file' => $aRow['image_path'],
            'path' => 'pages.url_image',
            'server_id' => $aRow['image_server_id'],
            'return_url' => true,
            'suffix' => '_200_square'
        ]);

        return $aRow;
    }

    public function isAdmin($aPage, $iUserId = null)
    {
        if (!isset($iUserId) || empty($iUserId)) {
            $iUserId = Phpfox::getUserId();
        }
        if (!Phpfox::isUser() || empty($aPage)) {
            return false;
        }

        if (is_numeric($aPage)) {
            $aPage = $this->getPage($aPage);
        }

        if (empty($aPage)) {
            $aPage = $this->getPage();
        }

        if (!isset($aPage['page_id'])) {
            return false;
        }

        if (isset($aPage['page_id']) && $aPage['page_id'] == Phpfox::getUserBy('profile_page_id')) {
            return true;
        }

        if ($aPage['user_id'] == $iUserId) {
            return true;
        }

        $iAdmin = (int)$this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages_admin'))
            ->where('page_id = ' . (int)$aPage['page_id'] . ' AND user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');

        if ($iAdmin) {
            return true;
        }

        return false;
    }

    public function isInvited($iPageId){
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(':pages_invite')
            ->where('page_id = ' . (int) $iPageId . ' AND type_id = 1 AND invited_user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');
        return ($iCnt) ? true : false;
    }
}
