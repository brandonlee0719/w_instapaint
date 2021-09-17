<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: category.class.php 5099 2013-01-07 19:01:38Z Raymond_Benc $
 */
abstract class Phpfox_Pages_Category extends Phpfox_Service
{
	/**
	 * @var array
	 */
	private $_aAllCategories = [];

	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('pages_category');
	}

	/**
	 * @return Phpfox_Pages_Facade
	 */
	abstract public function getFacade();
	
	public function getCategories()
	{
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('pages_type'))
			->where('is_active = 1 AND item_type = ' . $this->getFacade()->getItemTypeId())
			->order('time_stamp DESC')
			->execute('getSlaveRows');

		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['sub_categories'] = $this->database()->select('*')
				->from(Phpfox::getT('pages_category'))
				->where('type_id = ' . $aRow['type_id'] . ' AND is_active = 1')
				->execute('getSlaveRows');
		}
		
		return $aRows;
	}

    /**
     * Get categories by type id
     *
     * @param $iTypeId
     * @param int $iCacheTime , default 5
     * @return array
     */
    public function getByTypeId($iTypeId, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set($this->getFacade()->getItemType() . '_category_type_' . (int)$iTypeId);

        if (!($aRows = $this->cache()->get($sCacheId, $iCacheTime))) {
            $aRows = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('type_id = ' . (int)$iTypeId . ' AND is_active = 1')
                ->order('ordering ASC')
                ->execute('getSlaveRows');
            foreach ($aRows as &$aRow) {
                $aRow['link'] = $aRow['url'] = Phpfox::permalink($this->getFacade()->getItemType() . '.sub-category',
                    $aRow['category_id'], $aRow['name']);
            }

            $this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group($this->getFacade()->getItemType(), $sCacheId);
        }

        return $aRows;
    }
	
	public function getById($iId)
	{
		$aRow = $this->database()->select('pc.*, pt.name AS type_name, pt.type_id')
			->from($this->_sTable, 'pc')
			->join(Phpfox::getT('pages_type'), 'pt', 'pt.type_id = pc.type_id')
			->where('pc.category_id = ' . (int) $iId . ' AND pc.is_active = 1')
			->execute('getSlaveRow');
		
		if (!isset($aRow['category_id']))
		{
			return false;
		}
		
		return $aRow;
	}

	/**
	 *
	 * @return array
	 */
	public function getAllCategories()
	{
		if(empty($this->_aAllCategories)){
			$aRows = $this->database()->select('pc.*, pt.name AS type_name, pt.type_id')
				->from($this->_sTable, 'pc')
				->join(Phpfox::getT('pages_type'), 'pt', 'pt.type_id = pc.type_id')
				->where('pc.is_active = 1')
				->where('pt.item_type = ' . $this->getFacade()->getItemTypeId())
				->execute('getSlaveRows');

			foreach($aRows as $aRow){
				$this->_aAllCategories[$aRow['category_id']] =  $aRow;
			}
		}

		return $this->_aAllCategories;

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
        if (($userId != Phpfox::getUserId() || $userId === null) && Phpfox::hasCallback($this->getFacade()->getItemType(),
                'getExtraBrowseConditions')
        ) {
            $extra_conditions .= Phpfox::callback($this->getFacade()->getItemType() . '.getExtraBrowseConditions',
                'pages');
        }

        Phpfox::getService('privacy')->buildPrivacy(
            array(
                'module_id' => $this->getFacade()->getItemType(),
                'alias' => 'pages',
                'field' => 'page_id',
                'table' => Phpfox::getT('pages'),
                'service' => $this->getFacade()->getItemType() . '.browse'
            ), 'pages.time_stamp DESC', 0, null, ' AND ' . $extra_conditions, false
        );

        $this->database()->unionFrom('pages');

        $this->database()->select('pages.*, pu.vanity_url, ' . Phpfox::getUserField('u2', 'profile_'))
            ->join(Phpfox::getT('user'), 'u2', 'u2.profile_page_id = pages.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id');

        $iPagesLimit && $this->database()->limit($iPagesLimit); // 0 for unlimited

        return $this->database()
            ->order('pages.time_stamp DESC')
            ->where($extra_conditions)->execute('getSlaveRows');
    }
	
	public function getForBrowse($iCategoryId = null, $bIncludePages = false, $userId = null, $iPagesLimit = null)
	{
        $this->getAllCategories();

        if ($iCategoryId > 0) {
            $aCategories = $this->database()->select('pc.*')
                ->from($this->_sTable, 'pc')
                ->where('pc.type_id = ' . (int)$iCategoryId . ' AND pc.is_active = 1')
                ->order('pc.ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aCategories as $iKey => $aCategory) {
                $aCategories[$iKey]['link'] = Phpfox::permalink($this->getFacade()->getItemType() . '.sub-category',
                    $aCategory['category_id'], $aCategory['name']);
                $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
            }

            return $aCategories;
        }

        $aCategories = $this->database()->select('pt.*')
            ->from(Phpfox::getT('pages_type'), 'pt')
            ->where('pt.is_active = 1 AND pt.item_type = ' . $this->getFacade()->getItemTypeId())
            ->order('pt.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aCategories as $iKey => $aCategory) {
            if ($bIncludePages) {
                $aCategories[$iKey]['pages'] = $this->getLatestPages($aCategory['type_id'], $userId, $iPagesLimit);
                foreach ($aCategories[$iKey]['pages'] as $iSubKey => $aRow) {
                    $aSubCategory = isset($this->_aAllCategories[$aRow['category_id']]) ? $this->_aAllCategories[$aRow['category_id']] : [];
                    if ($aSubCategory) {
                        $aCategories[$iKey]['pages'][$iSubKey]['category_name'] = $aSubCategory['name'];
                    } else {
                        $aCategories[$iKey]['pages'][$iSubKey]['category_name'] = '';
                    }
                    if ($this->getFacade()->getItemType() == 'groups') {
                        $members = $this->getFacade()->getItems()->getMembers($aRow['page_id'], 4);
                        if (isset($members[1])) {
                            $aCategories[$iKey]['pages'][$iSubKey]['members'] = $members[1];
                        }
                    }

                    $aCategories[$iKey]['pages'][$iSubKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'],
                        $aRow['title'], $aRow['vanity_url']);

                    // check manage/delete for each page
                    $bCanModerate = $this->getFacade()->getUserParam('can_moderate_pages');
                    if ($bCanModerate === null) {
                        $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages') || $this->getFacade()->getUserParam('can_edit_all_pages') || $this->getFacade()->getUserParam('can_delete_all_pages');
                    }

                    if (Phpfox::isAdmin() || $bCanModerate || Phpfox::getUserId() == $aCategories[$iKey]['pages'][$iSubKey]['user_id']) {
                        $aCategories[$iKey]['pages'][$iSubKey]['manage'] = true;
                    } else {
                        $aCategories[$iKey]['pages'][$iSubKey]['manage'] = false;
                    }
                }

                // get total pages for each category
                $aCategories[$iKey]['total_pages'] = $this->getFacade()->getItems()->getItemsByCategory($aCategory['type_id'],
                    false, $this->getFacade()->getItemTypeId(), $userId, true);
            }
            $aCategories[$iKey]['link'] = Phpfox::permalink($this->getFacade()->getItemType() . '.category',
                $aCategory['type_id'], $aCategory['name']);

            // get sub categories
            $aCategories[$iKey]['sub'] = $this->getByTypeId($aCategory['type_id']);
        }

        return $aCategories;
	}	
	
	public function getForAdmin($iTypeId)
	{
		$aRows = $this->database()->select('*')
			->from($this->_sTable)
			->where('type_id = ' . (int) $iTypeId)
			->order('ordering ASC')
			->execute('getSlaveRows');	
		return $aRows;
	}	
	
	public function getForEdit($iId)
	{
		$aRow = $this->database()->select('*')
			->from(Phpfox::getT('pages_category'))
			->where('category_id = ' . (int) $iId)
			->execute('getSlaveRow');

		if (!isset($aRow['category_id']))
		{
			return false;
		}

        //Support legacy phrases
        if (substr($aRow['name'], 0, 7) == '{phrase' && substr($aRow['name'], -1) == '}') {
            $aRow['name'] = preg_replace('/\s+/', ' ', $aRow['name']);
            $aRow['name'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['name']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage){
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }
		
		return $aRow;
	}	
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_category_category__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

    /**
     * Move sub category to upper level
     * @param $iSubCategoryId
     * @param $iItemType
     */
	public function moveSubCategoryToUpperLevel($iSubCategoryId, $iItemType)
    {
        $aSubCategory = $this->getById($iSubCategoryId);
        // delete old
        db()->delete(':pages_category', 'category_id = ' . $iSubCategoryId);
        // insert to upper level
        $iNewCategoryId = Phpfox::getService('pages.process')->addCategory([
            'phrase_var_name' => $aSubCategory['name'],
            'is_active' => $aSubCategory['is_active'],
            'item_type' => $iItemType
        ]);

        // update sub category's items
        Phpfox::getService('pages')->moveItemsToAnotherCategory($iSubCategoryId, $iNewCategoryId, true, false, $iItemType);
    }
}