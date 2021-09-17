<?php

namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Category;

/**
 * Class Category
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Category extends Phpfox_Pages_Category
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }

    public function getForBrowse(
        $iCategoryId = null,
        $bIncludePages = false,
        $userId = null,
        $iPagesLimit = null,
        $sView = ''
    ) {
        $this->getAllCategories();

        if ($iCategoryId > 0) {
            $aCategories = $this->database()->select('pc.*')
                ->from($this->_sTable, 'pc')
                ->where('pc.type_id = ' . (int)$iCategoryId . ' AND pc.is_active = 1')
                ->order('pc.ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aCategories as $iKey => $aCategory) {
                $aCategories[$iKey]['link'] = Phpfox::permalink('groups.sub-category',
                    $aCategory['category_id'], $aCategory['name']);
                $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
            }

            return $aCategories;
        }

        $aCategories = $this->database()->select('pt.*')
            ->from(Phpfox::getT('pages_type'), 'pt')
            ->where('pt.is_active = 1 AND pt.item_type = 1')
            ->order('pt.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aCategories as $iKey => $aCategory) {
            if ($bIncludePages) {
                $aCategories[$iKey]['pages'] = $this->getLatestPages($aCategory['type_id'], $userId, $iPagesLimit);

                foreach ($aCategories[$iKey]['pages'] as $iSubKey => &$aPage) {
                    $aAllCategories = $this->getAllCategories();
                    $aSubCategory = isset($aAllCategories[$aPage['category_id']]) ? $aAllCategories[$aPage['category_id']] : [];
                    if ($aSubCategory) {
                        $aPage['category_name'] = $aSubCategory['name'];
                        $aPage['category_link'] = Phpfox::permalink('groups.sub-category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    } else {
                        $aPage['category_name'] = '';
                    }

                    $aPage['link'] = Phpfox::getService('groups')->getUrl($aPage['page_id'],
                        $aPage['title'], $aPage['vanity_url']);

                    // check permission for each action
                    Phpfox::getService('groups')->getActionsPermission($aPage, $sView);

                    // pending request to be member
                    $aPage['joinRequested'] = Phpfox::getService('groups')->joinGroupRequested($aPage['page_id']);
                }

                // get total pages for each category
                $aCategories[$iKey]['total_pages'] = Phpfox::getService('groups')->getItemsByCategory($aCategory['type_id'],
                    false, 1, $userId, true, $sView);
            }

            if ($sView) {
                $aCategories[$iKey]['link'] = Phpfox::permalink(['groups.category', 'view' => $sView], $aCategory['type_id'],
                    $aCategory['name']);
            } else {
                $aCategories[$iKey]['link'] = Phpfox::permalink('groups.category', $aCategory['type_id'], $aCategory['name']);
            }

            // get sub categories
            $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
            $aCategories[$iKey]['image_path'] = sprintf($aCategories[$iKey]['image_path'], '_200');
        }

        return $aCategories;
    }

    /**
     * Get lastest pages
     * @param $iId
     * @param null $userId
     * @param int $iPagesLimit , number of page want to limit, 0 for unlimited
     * @return array|int|string
     */
    public function getLatestPages($iId, $userId = null, $iPagesLimit = 8)
    {
        $extra_conditions = 'pages.type_id = ' . (int)$iId . ($userId ? ' AND pages.user_id = ' . (int)$userId : '');
        if (($userId != Phpfox::getUserId() || $userId === null) && Phpfox::hasCallback(Phpfox::getService('groups.facade')->getItemType(),
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
            ), 'pages.time_stamp DESC', 0, null, ' AND ' . $extra_conditions, false
        );

        $this->database()->unionFrom('pages');

        $this->database()->select('pages.*, pt.text, pt.text_parsed, l.like_id AS is_liked, pu.vanity_url, ' . Phpfox::getUserField('u2',
                'profile_'))
            ->join(Phpfox::getT('user'), 'u2', 'u2.profile_page_id = pages.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id')
            ->leftJoin(':pages_text', 'pt', 'pt.page_id = pages.page_id')
            ->leftJoin(':like', 'l',
                'l.type_id = \'groups\' AND l.item_id = pages.page_id AND l.user_id = ' . Phpfox::getUserId());

        $iPagesLimit && $this->database()->limit($iPagesLimit); // 0 for unlimited

        return $this->database()
            ->order('pages.time_stamp DESC')
            ->where($extra_conditions)->execute('getSlaveRows');
    }

    /**
     * Move sub categories to another type
     *
     * @param $iOldTypeId
     * @param $iNewTypeId
     */
    public function moveSubCategoriesToAnotherType($iOldTypeId, $iNewTypeId)
    {
        // update type id of sub-categories
        db()->update(':pages_category', [
            'type_id' => $iNewTypeId
        ], [
            'type_id' => $iOldTypeId
        ]);
    }

    /**
     * Get category name
     *
     * @param $iCategoryId
     * @return string
     */
    public function getCategoryName($iCategoryId)
    {
        return _p(db()->select('name')->from(':pages_category')->where(['category_id' => $iCategoryId])->executeField());
    }
}
