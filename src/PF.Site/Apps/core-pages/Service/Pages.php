<?php

namespace Apps\Core_Pages\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Pages extends \Phpfox_Pages_Pages
{
    private $_aPhotoPicSizes = [50, 120, 200, 500, 1024];

    /**
     * @return Facade|object
     */
    public function getFacade()
    {
        return new Facade();
    }

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
                    'url' => $this->getUrl($aWidget['page_id']) . $aWidget['url_title'] . '/',
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
     * @param $mId
     * @return array|int|string
     */
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

        $this->_aRow = $this->database()->select('p.*, u.user_image as image_path, p.image_path as pages_image_path, u.user_id as page_user_id, p.use_timeline, pc.claim_id, pu.vanity_url, pg.name AS category_name, pg.page_type, pt.text_parsed AS text, u.full_name, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, p_type.name AS parent_category_name, ' . Phpfox::getUserField('u2',
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

        $type = $this->getFacade()->getType()->getById($this->_aRow['type_id']);
        if (empty($this->_aRow['category_name']) && $type) {
            $this->_aRow['category_name'] = $type['name'];
            $this->_aRow['category_link'] = Phpfox::permalink('pages.category', $this->_aRow['type_id'], $type['name']);
        } else {
            $this->_aRow['type_link'] = Phpfox::permalink('pages.category', $this->_aRow['type_id'], $type['name']);
            $this->_aRow['category_link'] = Phpfox::permalink('pages.sub-category', $this->_aRow['category_id'],
                $this->_aRow['category_name']);
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

        return $this->_aRow;
    }

    /**
     * Get my pages | Get my pages total
     * @param bool $bIsCount
     * @param bool $bIncludePending
     * @return array|int|string
     */
    public function getMyPages($bIsCount = false, $bIncludePending = false)
    {
        if ($bIsCount) {
            return $this->database()->select('count(*)')->from($this->_sTable)
                ->where(array_merge([
                    'user_id' => Phpfox::getUserId(),
                    'item_type' => $this->getFacade()->getItemTypeId()
                ], $bIncludePending ? [] : ['view_id' => 0,]))
                ->executeField();
        } else {
            $aRows = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where(array_merge([
                    'p.user_id' => Phpfox::getUserId(),
                    'p.item_type' => $this->getFacade()->getItemTypeId()
                ], $bIncludePending ? [] : ['p.view_id' => 0]))
                ->order('p.time_stamp DESC')
                ->execute('getSlaveRows');

            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']);
            }

            return $aRows;
        }
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
            'item_type' => $iItemType
        ];

        if ($bIsSub) {
            $aConds['category_id'] = $iCategoryId;
        } else {
            $aConds['type_id'] = $iCategoryId;
        }
        switch ($sView) {
            case '':
                $aConds['view_id'] = 0;

                break;
            case 'my':
                $aConds['user_id'] = $iUserId;

                break;
            case 'friend':
                $aFriends = Phpfox::getService('friend')->getFromCache();
                $sFriendsList = implode(',', array_column($aFriends, 'friend_id'));
                $aConds['user_id'] = [
                    'in' => $sFriendsList
                ];
                $aConds['view_id'] = 0;

                break;
            case 'pending':
                $aConds['view_id'] = 1;

                break;
            default:
                break;
        }

        if ($bGetCount) {
            return db()->select('COUNT(*)')
                ->from(':pages')
                ->where($aConds)
                ->executeField();
        } else {
            return db()->select('*')
                ->from(':pages')
                ->where($aConds)
                ->executeRows();
        }
    }

    /**
     * Get all pages count
     * @return int
     */
    public function getAllPagesCount()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_service_pages_getallcount__start')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('pages_all_count');
        if (!$iAllPagesCount = $this->cache()->get($sCacheId, 1)) {
            $iAllPagesCount = db()->select("COUNT(*)")
                ->from($this->_sTable)
                ->where(['view_id' => 0])
                ->executeField();

            $this->cache()->save($sCacheId, $iAllPagesCount);
        }

        return $iAllPagesCount;
    }

    /**
     * Get friend pages count
     * @param null $iUserId
     * @return int
     */
    public function getFriendPagesCount($iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('page.component_service_page_getallcount__start')) ? eval($sPlugin) : false);

        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $sCacheId = $this->cache()->set('page_friend_count_' . (int)$iUserId);
        if (!$iFriendPagesCount = $this->cache()->get($sCacheId, 1)) {
            list(, $aFriends) = Phpfox::getService('friend')->get(array('AND friend.user_id = ' . (int)Phpfox::getUserId()),
                '', '', false);

            if (empty($aFriends)) {
                $iFriendPagesCount = 0;
            } else {
                $iFriendPagesCount = db()->select("COUNT(*)")
                    ->from($this->_sTable)
                    ->where('view_id = 0 AND user_id IN (' . implode(',', array_column($aFriends, 'user_id')) . ')')
                    ->executeField();
            }

            $this->cache()->save($sCacheId, $iFriendPagesCount);
        }

        return $iFriendPagesCount;
    }

    /**
     * Get page info
     * @param $iPageId
     * @param bool $bGetParsed
     * @return string
     */
    public function getInfo($iPageId, $bGetParsed = false)
    {
        if ($bGetParsed) {
            return db()->select('text_parsed')->from(':pages_text')->where(['page_id' => $iPageId])->executeField();
        }

        return db()->select('text')->from(':pages_text')->where(['page_id' => $iPageId])->executeField();
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

        $aPages = $this->database()->select('p.*, pt.name as type_name, pc.name as category_name, ph.destination as cover_image_path, ph.server_id as cover_image_server_id, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('like'), 'l')
            ->join(Phpfox::getT('pages'), 'p',
                'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->leftJoin(':pages_type', 'pt', 'p.type_id = pt.type_id')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int)$iUserId . $sConds)
            ->group('p.page_id', true)// fixes displaying duplicate pages if there are duplicate likes
            ->order('l.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aPages as $iKey => $aPage) {
            $aPages[$iKey]['is_app'] = false;
            $aPages[$iKey]['is_user_page'] = true;
            $aPages[$iKey]['user_image'] = sprintf($aPage['image_path'], '_200_square');
            $aPages[$iKey]['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        }

        return array($iCnt, $aPages);
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
                'phrase' => $this->getFacade()->getPhrase('info'),
                'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']) . 'info',
                'icon' => 'misc/comment.png',
                'landing' => 'info'
            ],
            [
                'phrase' => $this->getFacade()->getPhrase('members'),
                'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']) . 'members',
                'icon' => 'misc/comment.png',
                'landing' => 'members'
            ]
        ];

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

    public function getMembers($iPageId, $iLimit = 20, $iPage = 1, $sSearch = '')
    {
        if (!Phpfox::isModule('like')) {
            return false;
        }
        $aWhere = [
            'l.type_id' => 'pages',
            'l.item_id' => intval($iPageId)
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

    public function getPageAdmins($iId = null, $iPage = 1, $iLimit = null, $sSearch = null)
    {
        if ($iId != null && empty($this->_aRow)) {
            $this->getForView($iId);
        }

        $aOwnerAdmin = $aPageAdmins = [];
        if ($iPage == 1) {
            foreach ($this->_aRow as $sKey => $mValue) {
                if (substr($sKey, 0, 6) == 'owner_') {
                    $aOwnerAdmin[0][str_replace('owner_', '', $sKey)] = $mValue;
                }
            }
            $iLimit--;
        }
        if ($sSearch && !empty($aOwnerAdmin) && stristr($aOwnerAdmin[0]['full_name'], $sSearch) === false) {
            $aOwnerAdmin = [];
        }

        if ($iLimit) {
            $this->database()->limit($iPage - 1, $iLimit);
        }

        $aPageAdmins = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('pages_admin'), 'pa')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where(array_merge([
                'pa.page_id' => intval($this->_aRow['page_id'])
            ], $sSearch ? ['u.full_name' => ['LIKE' => "%$sSearch%"]] : []))
            ->executeRows();

        return array_merge($aOwnerAdmin, $aPageAdmins);
    }

    public function getPageAdminsCount($iPageId)
    {
        return db()->select('COUNT(*)')->from(':pages_admin')->where(['page_id' => $iPageId])->executeField() + 1;
    }

    public function getPendingUsers($iPageId, $bIsCount = false, $iPage = 1, $iLimit = null, $sSearch = null)
    {
        $this->database()
            ->from(Phpfox::getT('pages_signup'), 'ps')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
            ->where(array_merge([
                'ps.page_id' => intval($iPageId)
            ], $sSearch ? ['u.full_name' => ['LIKE', "%$sSearch%"]] : []));

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
        $aItems = Phpfox::getService('pages')->getItemsByCategory($iOldCategoryId, $bOldIsSub, $iItemType);
        if ($bNewIsSub) {
            // get type id
            $iTypeId = Phpfox::getService('pages.category')->getById($iNewCategoryId)['type_id'];
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
     * Get upload photo params, support dropzone
     *
     * @return array
     */
    public function getUploadPhotoParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('pages.max_upload_size_pages');
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
        (($sPlugin = Phpfox_Plugin::get('pages.service_pages_getphotopicsizes')) ? eval($sPlugin) : false);

        return $this->_aPhotoPicSizes;
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

        return db()->select('p.*, pc.name as category, pu.vanity_url, u.*, ph.destination as cover_image_path, ph.server_id as cover_image_server_id')
            ->from($this->_sTable, 'p')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(':pages_url', 'pu', 'p.page_id = pu.page_id')
            ->leftJoin(':user', 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->where("p.page_id != $iPageid AND p.type_id = $aPage[type_id] AND p.view_id = 0")
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

        if (!$this->isAdmin($aRow) && !Phpfox::getUserParam('pages.can_edit_all_pages')) {
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
        !defined('PHPFOX_PAGES_EDIT_ID') && define('PHPFOX_PAGES_EDIT_ID', $aRow['page_id']);
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
        if (empty($iUserId)) {
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

        if ($aPage['user_id'] == $iUserId || $aPage['page_id'] == Phpfox::getUserBy('profile_page_id')) {
            return true;
        }

        $iAdmin = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages_admin'))
            ->where('page_id = ' . (int)$aPage['page_id'] . ' AND user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');

        return !!$iAdmin;
    }

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

        if (!$iPage && !empty($this->_aRow['page_id'])) {
            $iPage = $this->_aRow['page_id'];
        }

        if (empty($iPage) && empty($this->_aRow['page_id'])) {
            return false;
        }

        $aPerms = $this->getPermsForPage($iPage);
        if (isset($aPerms[$sPerm])) {
            switch ((int)$aPerms[$sPerm]) {
                case 1:
                    if (!$this->isMember($iPage)) {
                        return false;
                    }
                    break;
                case 2:
                    if (!$this->isAdmin($iPage)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    /**
     * Get page permissions
     * @param $iPage
     * @return array
     */
    public function getPerms($iPage)
    {
        $aCallbacks = Phpfox::massCallback('getPagePerms');
        $aPerms = array();
        $aUserPerms = $this->getPermsForPage($iPage);
        foreach ($aCallbacks as $aCallback) {
            foreach ($aCallback as $sId => $sPhrase) {
                $aPerms[] = array(
                    'id' => $sId,
                    'phrase' => $sPhrase,
                    'is_active' => (isset($aUserPerms[$sId]) ? $aUserPerms[$sId] : '0')
                );
            }
        }

        return $aPerms;
    }

    public function getActionsPermission(&$aPage, $sView = '')
    {
        $aPage['bCanApprove'] = $sView == 'pending' && $aPage['view_id'] == 1 && Phpfox::getUserParam('pages.can_approve_pages');
        $aPage['bCanEdit'] = Phpfox::getService('pages')->isAdmin($aPage) || Phpfox::getUserParam('pages.can_edit_all_pages');
        $aPage['bCanDelete'] = Phpfox::getUserId() == $aPage['user_id'] || Phpfox::getUserParam('pages.can_delete_all_pages');
        $aPage['bShowItemActions'] = $aPage['bCanApprove'] || $aPage['bCanEdit'] || $aPage['bCanDelete'];
    }
}
