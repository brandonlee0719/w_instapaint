<?php

namespace Apps\Core_Pages\Service;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Category extends \Phpfox_Pages_Category
{
    /**
     * @return Facade
     */
    public function getFacade()
    {
        return \Phpfox::getService('pages.facade');
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
        if (($userId != Phpfox::getUserId() || $userId === null) && Phpfox::hasCallback(Phpfox::getService('pages.facade')->getItemType(),
                'getExtraBrowseConditions')
        ) {
            $extra_conditions .= Phpfox::callback(Phpfox::getService('pages.facade')->getItemType() . '.getExtraBrowseConditions',
                'pages');
        }

        Phpfox::getService('privacy')->buildPrivacy(
            array(
                'module_id' => Phpfox::getService('pages.facade')->getItemType(),
                'alias' => 'pages',
                'field' => 'page_id',
                'table' => Phpfox::getT('pages'),
                'service' => Phpfox::getService('pages.facade')->getItemType() . '.browse'
            ), 'pages.time_stamp DESC', 0, null, ' AND ' . $extra_conditions, false
        );

        $this->database()->unionFrom('pages');

        $this->database()->select('pages.*, pt.text, pt.text_parsed, l.like_id AS is_liked, pu.vanity_url, ' . Phpfox::getUserField('u2',
                'profile_'))
            ->join(Phpfox::getT('user'), 'u2', 'u2.profile_page_id = pages.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id')
            ->leftJoin(':pages_text', 'pt', 'pt.page_id = pages.page_id')
            ->leftJoin(':like', 'l',
                'l.type_id = \'pages\' AND l.item_id = pages.page_id AND l.user_id = ' . Phpfox::getUserId());

        $iPagesLimit && $this->database()->limit($iPagesLimit); // 0 for unlimited

        return $this->database()
            ->order('pages.time_stamp DESC')
            ->where($extra_conditions)->execute('getSlaveRows');
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
                $aCategories[$iKey]['link'] = Phpfox::permalink('pages.sub-category',
                    $aCategory['category_id'], $aCategory['name']);
                $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
            }

            return $aCategories;
        }

        $aCategories = $this->database()->select('pt.*')
            ->from(Phpfox::getT('pages_type'), 'pt')
            ->where('pt.is_active = 1 AND pt.item_type = 0')
            ->order('pt.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aCategories as $iKey => $aCategory) {
            if ($bIncludePages) {
                $aCategories[$iKey]['pages'] = $this->getLatestPages($aCategory['type_id'], $userId, $iPagesLimit);
                foreach ($aCategories[$iKey]['pages'] as $iSubKey => $aRow) {
                    $aAllCategories = $this->getAllCategories();
                    $aSubCategory = isset($aAllCategories[$aRow['category_id']]) ? $aAllCategories[$aRow['category_id']] : [];
                    if ($aSubCategory) {
                        $aCategories[$iKey]['pages'][$iSubKey]['category_name'] = $aSubCategory['name'];
                        $aCategories[$iKey]['pages'][$iSubKey]['category_link'] = Phpfox::permalink('pages.sub-category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    } else {
                        $aCategories[$iKey]['pages'][$iSubKey]['category_name'] = '';
                    }

                    $aCategories[$iKey]['pages'][$iSubKey]['link'] = Phpfox::getService('pages')->getUrl($aRow['page_id'],
                        $aRow['title'], $aRow['vanity_url']);

                    // check manage/delete for each page
                    $bCanModerate = Phpfox::getUserParam('pages.can_approve_pages') || Phpfox::getUserParam('pages.can_edit_all_pages') || Phpfox::getUserParam('pages.can_delete_all_pages');

                    if (Phpfox::isAdmin() || $bCanModerate || Phpfox::getUserId() == $aCategories[$iKey]['pages'][$iSubKey]['user_id']) {
                        $aCategories[$iKey]['pages'][$iSubKey]['manage'] = true;
                    } else {
                        $aCategories[$iKey]['pages'][$iSubKey]['manage'] = false;
                    }

                    // check manage/delete for each page
                    $aCategories[$iKey]['pages'][$iSubKey]['canApprove'] = in_array($sView, ['my', 'pending']) && $aRow['view_id'] && Phpfox::getUserParam('pages.can_approve_pages');
                    if (Phpfox::getUserId() == $aRow['user_id']) {
                        $aCategories[$iKey]['pages'][$iSubKey]['canEdit'] = true;
                        $aCategories[$iKey]['pages'][$iSubKey]['canDelete'] = true;
                    } else {
                        $aCategories[$iKey]['pages'][$iSubKey]['canEdit'] = Phpfox::getUserParam('pages.can_edit_all_pages') ||
                            Phpfox::getService('pages')->isAdmin($aRow, Phpfox::getUserId());
                        $aCategories[$iKey]['pages'][$iSubKey]['canDelete'] = Phpfox::getUserParam('pages.can_delete_all_pages');
                    }
                }

                // get total pages for each category
                $aCategories[$iKey]['total_pages'] = Phpfox::getService('pages')->getItemsByCategory($aCategory['type_id'],
                    false, 0, $userId, true, $sView);
            }

            if ($sView) {
                $aCategories[$iKey]['link'] = Phpfox::permalink(['pages.category', 'view' => $sView], $aCategory['type_id'],
                    $aCategory['name']);
            } else {
                $aCategories[$iKey]['link'] = Phpfox::permalink('pages.category', $aCategory['type_id'], $aCategory['name']);
            }
            
            $aCategories[$iKey]['image_path'] = sprintf($aCategories[$iKey]['image_path'], '_200');

            // get sub categories
            $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
        }

        return $aCategories;
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
}
