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
 * @version 		$Id: pages.class.php 7234 2014-03-27 14:40:29Z Fern $
 */
abstract class Phpfox_Pages_Pages extends Phpfox_Service
{
	protected $_bIsInViewMode = false;

	protected $_aPage = null;
	
	protected $_aRow = array();
	
	protected $_bIsInPage = false;
	
	protected $_aWidgetMenus = array();
	protected $_aWidgetUrl = array();
	protected $_aWidgetBlocks = array();
	protected $_aWidgets = array();
	protected $_aWidgetEdit = array();
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('pages');
	}

	/**
	 * @return Phpfox_Pages_Facade
	 */
	abstract public function getFacade();

	public function isTimelinePage($iPageId)
	{
		return ((int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('pages'))
			->where('page_id = ' . (int) $iPageId . ' AND use_timeline = 1')
			->execute('getSlaveField') ? true : false);	
	}
	
	public function setMode($bMode = true)
	{
		$this->_bIsInViewMode = $bMode;
	}
	
	public function isViewMode()
	{
		return (bool) $this->_bIsInViewMode;
	}
	
	public function setIsInPage()
	{
		$this->_bIsInPage = true;		
	}
	
	public function isInPage()
	{
		return $this->_bIsInPage;
	}
	
	public function buildWidgets($iId)
	{		
		if (!$this->getFacade()->getItems()->hasPerm($iId, $this->getFacade()->getItemType() . '.view_browse_widgets'))
		{
			return;
		}
		
		$aWidgets = $this->database()->select('pw.*, pwt.text_parsed AS text')
			->from(Phpfox::getT('pages_widget'), 'pw')
			->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
			->where('pw.page_id = ' . (int) $iId)
			->execute('getSlaveRows');

		foreach ($aWidgets as $aWidget)
		{
			$this->_aWidgetEdit[] = array(
				'widget_id' => $aWidget['widget_id'],
				'title' => $aWidget['title']
			);

			if (!$aWidget['is_block'])
            {
                $this->_aWidgetMenus[] = array(
                    'phrase' => $aWidget['menu_title'],
                    'url' => $this->getUrl($aWidget['page_id'], $this->_aRow['title'], $this->_aRow['vanity_url']) . $aWidget['url_title'] . '/',
                    'landing' => $aWidget['url_title'],
                    'icon_pass' => (empty($aWidget['image_path']) ? false : true),
                    'icon' => $aWidget['image_path'],
                    'icon_server' => $aWidget['image_server_id']
                );
            }
			
			$this->_aWidgetUrl[$aWidget['url_title']] = $aWidget['widget_id'];
			
			if ($aWidget['is_block'])
			{
				$this->_aWidgetBlocks[] = $aWidget;
			}
			else
			{
				$this->_aWidgets[$aWidget['url_title']] = $aWidget;
			}			
		}
	}	
	
	public function getForEditWidget($iId)
	{
		$aWidget = $this->database()->select('pw.*, pwt.text_parsed AS text')
			->from(Phpfox::getT('pages_widget'), 'pw')
			->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
			->where('pw.widget_id = ' . (int) $iId)
			->execute('getSlaveRow');	
		
		if (!isset($aWidget['widget_id']))
		{
			return false;
		}
		
		$aPage = $this->getPage($aWidget['page_id']);
		
		if (!isset($aPage['page_id']))
		{
			return false;
		}
		
		if (!$this->isAdmin($aPage))
		{
			if (!$this->getFacade()->getUserParam('can_moderate_pages'))
			{
				return false;
			}
		}

		$aWidget['text'] = str_replace(array('<br />', '<br>', '<br/>'), "\n", $aWidget['text']);
		
		return $aWidget;
	}
	
	public function getWidgetsForEdit()
	{
		return $this->_aWidgetEdit;
	}
	
	public function isWidget($sUrl)
	{
		return (isset($this->_aWidgetUrl[$sUrl]) ? true : false);
	}
	
	public function getWidget($sUrl)
	{
		return $this->_aWidgets[$sUrl];
	}
	
	public function getWidgetBlocks()
	{
		return $this->_aWidgetBlocks;
	}
	
	public function getForProfile($iUserId, $iLimit = 0, $bNoCount = false, $sConds = '')
	{
		if ($bNoCount == false)
      	{
            $iCnt = $this->database()->select('p.*, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
				->from(Phpfox::getT('like'), 'l')			
				->join(Phpfox::getT('pages'), 'p', 'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
				->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
				->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
				->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int) $iUserId . $sConds)
                ->group('p.page_id', true) // fixes displaying duplicate pages if there are duplicate likes
                ->order('l.time_stamp DESC')
				->execute('getSlaveRows');
            $iCnt = count($iCnt);
            
        }
		
		if ($iLimit) {
			$aPages = $this->database()->select('p.*, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
				->from(Phpfox::getT('like'), 'l')			
				->join(Phpfox::getT('pages'), 'p', 'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
				->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
				->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
				->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int) $iUserId . $sConds)
                ->group('p.page_id', true) // fixes displaying duplicate pages if there are duplicate likes
                ->order('l.time_stamp DESC')
				->limit($iLimit)
				->execute('getSlaveRows');
		}		
		else {
			$aPages = $this->database()->select('p.*, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
				->from(Phpfox::getT('like'), 'l')			
				->join(Phpfox::getT('pages'), 'p', 'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
				->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
				->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
				->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int) $iUserId . $sConds)
                ->group('p.page_id', true) // fixes displaying duplicate pages if there are duplicate likes
                ->order('l.time_stamp DESC')
				->execute('getSlaveRows');
		}		
	
		foreach ($aPages as $iKey => $aPage)
		{
			$aPages[$iKey]['is_app'] = false;

			$aPages[$iKey]['is_user_page'] = true;
			$aPages[$iKey]['user_image'] = $aPage['image_path'];
			$aPages[$iKey]['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		}
		
		return array($iCnt, $aPages);
	}
	
	public function getForView($mId)
	{
		if ($this->_aPage !== null)
		{
			$mId = $this->_aPage['page_id'];
		}

		$pageUserId  = Phpfox::getUserId();
		
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = p.user_id AND f.friend_user_id = " . $pageUserId);
		}			
		
		if(Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.item_id = p.page_id AND l.user_id = ' . $pageUserId);
		}
		
		$this->_aRow = $this->database()->select('p.*, u.user_image as image_path, p.image_path as pages_image_path, u.user_id as page_user_id, p.use_timeline, pc.claim_id, pu.vanity_url, pg.name AS category_name, pg.page_type, pt.text_parsed AS text, u.full_name, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, p_type.name AS parent_category_name, ' . Phpfox::getUserField('u2', 'owner_'))
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
			->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pg.type_id')
			->leftJoin(Phpfox::getT('theme_style'), 'ts', 'ts.style_id = p.designer_style_id')
			->leftJoin(Phpfox::getT('theme'), 't', 't.theme_id = ts.theme_id')				
			->leftJoin(Phpfox::getT('pages_claim'), 'pc','pc.page_id = p.page_id AND pc.user_id = ' . Phpfox::getUserId())
			->where('p.page_id = ' . (int) $mId . ' AND p.item_type = ' . $this->getFacade()->getItemTypeId())
			->execute('getSlaveRow');

		if (!isset($this->_aRow['page_id']))
		{
			return false;
		}

		$this->_aRow['is_page'] = true;
		$this->_aRow['is_admin'] = $this->isAdmin($this->_aRow);		
		$this->_aRow['link'] = $this->getFacade()->getItems()->getUrl($this->_aRow['page_id'], $this->_aRow['title'], $this->_aRow['vanity_url']);		
		
		if (($this->_aRow['page_type'] == '1' || $this->_aRow['item_type'] != '0') && $this->_aRow['reg_method'] == '1')
		{
			$this->_aRow['is_reg'] = (int) $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_signup'))
				->where('page_id = ' . (int) $this->_aRow['page_id'] . ' AND user_id = ' . Phpfox::getUserId())
				->execute('getSlaveField');
		}
		
		if ($this->_aRow['reg_method'] == '2' && Phpfox::isUser())
		{
			$this->_aRow['is_invited'] = (int) $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_invite'))
				->where('page_id = ' . (int) $this->_aRow['page_id'] . ' AND invited_user_id = ' . Phpfox::getUserId())
				->execute('getSlaveField');
			
			if (!$this->_aRow['is_invited'])
			{
				unset($this->_aRow['is_invited']);
			}
		}

		if (empty($this->_aRow['category_name']) && ($type = $this->getFacade()->getType()->getById($this->_aRow['type_id']))) {
			$this->_aRow['category_name'] = $type['name'];
		}

		if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			$this->_aRow['is_liked'] = true;
		}
		
		// Issue with like/join button
		// Still not defined
		if (!isset($this->_aRow['is_liked']))
		{
			// make it false: not liked or joined yet
			$this->_aRow['is_liked'] = false;
		}
		
		if ($this->_aRow['app_id'])
		{			
			if ($this->_aRow['aApp'] = Phpfox::getService('apps')->getForPage($this->_aRow['app_id']))
			{
				$this->_aRow['is_app'] = true;
				$this->_aRow['title'] = $this->_aRow['aApp']['app_title'];
				$this->_aRow['category_name'] = 'App';
			}
		}
		else
		{
			$this->_aRow['is_app'] = false;
		}		
		
		return $this->_aRow;
	}

	public function getActivePage() {
		return $this->_aRow;
	}
	
	public function isMember($iPage)
	{
		if (empty($this->_aRow))
		{
			$this->_aRow = $this->getForView($iPage);
		}
		if (!isset($this->_aRow['page_id']))
		{
			return false;
		}

		if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			return true;
		}		
		
		return ((isset($this->_aRow['is_liked']) && $this->_aRow['is_liked']) ? true : false);
	}
	
	public function getPageAdmins($iId = null)
	{
		if ($iId != null && empty($this->_aRow)) {
			$this->getForView($iId);
		}
		$aOwnerAdmin = array();
		foreach ($this->_aRow as $sKey => $mValue)
		{
			if (substr($sKey, 0, 6) == 'owner_')
			{
				$aOwnerAdmin[0][str_replace('owner_', '', $sKey)] = $mValue;
			}
		}
		
		$aPageAdmins = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('pages_admin'), 'pa')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
			->where('pa.page_id = ' . (int) $this->_aRow['page_id'])
			->execute('getSlaveRows');
		
		$aAdmins = array_merge($aOwnerAdmin, $aPageAdmins);	

		return $aAdmins;
	}
	
	public function isAdmin($aPage, $iUserId = null)
	{
        if (!isset($iUserId) || empty($iUserId)){
            $iUserId = Phpfox::getUserId();
        }
		if (!Phpfox::isUser() || empty($aPage))
		{
			return false;
		}
		
		if (is_numeric($aPage))
		{
			$aPage = $this->getPage($aPage);
		}

		if (empty($aPage))
		{
			$aPage = $this->getPage();
		}
		
        if (!isset($aPage['page_id']))
        {
            return false;
        }
        
		if (isset($aPage['page_id']) && $aPage['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			return true;
		}

		if ($aPage['user_id'] == $iUserId)
		{
			return true;
		}

		if (Phpfox::getService('user')->isAdminUser($iUserId, false))
		{
			return true;
		}

		$iAdmin = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('pages_admin'))
			->where('page_id = ' . (int) $aPage['page_id'] . ' AND user_id = ' . (int) $iUserId)
			->execute('getSlaveField');
		
		if ($iAdmin)
		{
			return true;
		}
		
		return false;
	}
	
	public function getPage($iId = null)
	{
		static $aRow = null;
		
		if (is_array($aRow) && $iId === null)
		{
			return $aRow;
		}
		
		if(Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.item_id = p.page_id AND l.user_id = ' . Phpfox::getUserId());
		}
		
		$aRow = $this->database()->select('p.*, pu.vanity_url, pg.name AS category_name, pg.page_type')
			->from($this->_sTable, 'p')			
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')		
			->where('p.page_id = ' . (int) $iId . ' AND p.item_type = ' . $this->getFacade()->getItemTypeId())
			->execute('getSlaveRow');
		
		if (empty($aRow) && $iId === null)
		{
			return false;
		}

		if (!isset($aRow['page_id']))
		{
			return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_looking_for'));
		}

		if (empty($aRow['category_name']) && ($type = $this->getFacade()->getType()->getById($aRow['type_id']))) {
			$aRow['category_name'] = $type['name'];
		}
		
		if (empty($this->_aRow))
		{
			$this->_aRow = $aRow;
		}
		
		if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			$this->_aRow['is_liked'] = true;
		}
		
		// Issue with like/join button
		// Still not defined
		if (!isset($this->_aRow['is_liked']))
		{
			// make it false: not liked or joined yet
			$this->_aRow['is_liked'] = false;
		}

		return $aRow;
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
                    'view_id' => 0,
                    'item_type' => $this->getFacade()->getItemTypeId()
                ], $bIncludePending ? ['user_id' => Phpfox::getUserId()] : []))
                ->executeField();
        } else {
            $aRows = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where(array_merge([
                    'p.user_id' => Phpfox::getUserId(),
                    'p.item_type' => $this->getFacade()->getItemTypeId()
                ], $bIncludePending ? ['p.view_id' => 0] : []))
                ->order('p.time_stamp DESC')
                ->execute('getSlaveRows');

            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']);
            }

            return $aRows;
        }
    }
	
	public function getUrl($iPageId, $sTitle = null, $sVanityUrl = null, $bIsGroup = false)
	{
		if ($sTitle === null && $sVanityUrl === null)
		{
			$aPage = $this->getPage($iPageId);
			$sTitle = $aPage['title'];
			$sVanityUrl = $aPage['vanity_url'];
		}
		
		if (!empty($sVanityUrl))
		{
			return Phpfox_Url::instance()->makeUrl($sVanityUrl);
		}

		return Phpfox_Url::instance()->makeUrl($this->getFacade()->getItemType(), $iPageId);
	}
	
	public function isPage($sUrl)
	{
		$aPage = $this->database()->select('pu.*')
			->from(Phpfox::getT('pages_url'), 'pu')
			->join(':pages', 'p', 'p.page_id = pu.page_id')
			->where('pu.vanity_url = \'' . $this->database()->escape($sUrl) . '\' AND p.item_type = '. $this->getFacade()->getItemTypeId())
			->execute('getSlaveRow');
		
		if (!isset($aPage['page_id']))
		{
			return false;
		}
		
		$this->_aPage = $aPage;
		
		return true;
	}

    public function getMenu($aPage)
    {
        $aMenus = array();

        if ($this->isAdmin($aPage)) {
            $iTotalPendingMembers = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_signup'))
                ->where('page_id = ' . (int)$aPage['page_id'])
                ->execute('getSlaveField');

            if ($iTotalPendingMembers > 0) {
                Phpfox_Template::instance()->assign('aSubPagesMenus', [
                    [
                        'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                                $aPage['vanity_url']) . 'pending/',
                        'title' => $this->getFacade()->getPhrase('pending_memberships') . '<span class="pending">&nbsp;(' . $iTotalPendingMembers . ')</span>'
                    ]
                ]);
            }
        }

        $aMenus[] = array(
            'phrase' => $this->getFacade()->getPhrase('home'),
            'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']) . (empty($aPage['landing_page']) ? '' : 'wall/'),
            'icon' => 'misc/comment.png',
            'landing' => ''
        );

        $aMenus[] = array(
            'phrase' => $this->getFacade()->getPhrase('info'),
            'url' => $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']) . 'info/',
            'icon' => 'misc/comment.png',
            'landing' => 'info'
        );

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

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getmenu')) {
            eval($sPlugin);
        }

        return $aMenus;
    }
	
	public function getForEdit($iId)
	{
		static $aRow = null;
		
		if (is_array($aRow))
		{
			return $aRow;
		}
		
		$aRow = $this->database()->select('p.*, pu.vanity_url, pt.text, pc.page_type, p_type.item_type')
			->from($this->_sTable, 'p')			
			->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pc', 'p.category_id = pc.category_id')
			->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pc.type_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')			
			->where('p.page_id = ' . (int) $iId)			
			->execute('getSlaveRow');

		if (!isset($aRow['page_id']))
		{
			return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_edit'));
		}
		
		if (!$this->isAdmin($aRow))
		{
			if (!$this->getFacade()->getUserParam('can_moderate_pages'))
			{
				return Phpfox_Error::set($this->getFacade()->getPhrase('you_are_unable_to_edit_this_page'));
			}
		}
		
		$this->_aRow = $aRow;
		
		$this->getFacade()->getItems()->buildWidgets($aRow['page_id']);
		
		$aRow['admins'] = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('pages_admin'), 'pa')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
			->where('pa.page_id = ' . (int) $aRow['page_id'])
			->execute('getSlaveRows');

        $aRow['admin_ids'] = [];
		foreach ($aRow['admins'] as $aAdmin) {
            $aRow['admin_ids'][] = $aAdmin['user_id'];
        }

        $aRow['admin_ids'] = json_encode($aRow['admin_ids']);

		$aMenus = $this->getMenu($aRow);		
		foreach ($aMenus as $iKey => $aMenu)
		{
			$aMenus[$iKey]['is_selected'] = false;
		}		
		if (!empty($aRow['landing_page']))
		{
			foreach ($aMenus as $iKey => $aMenu)
			{
				if ($aMenu['landing'] == $aRow['landing_page'])
				{
					$aMenus[$iKey]['is_selected'] = true;
				}
			}
		}

		$aRow['landing_pages'] = $aMenus;
		
		if ($aRow['app_id'])
		{			
			if ($aRow['aApp'] = Phpfox::getService('apps')->getForPage($aRow['app_id']))
			{
				$aRow['is_app'] = true;
				$aRow['title'] = $aRow['aApp']['app_title'];				
			}
		}
		else
		{
			$aRow['is_app'] = false;
		}			
		if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getforedit_1')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
		
		define('PHPFOX_PAGES_EDIT_ID', $aRow['page_id']);
		
		
		$aRow['location']['name'] = $aRow['location_name'];
		return $aRow;		
	}
	
	public function getCurrentInvites($iPageId)
	{
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('pages_invite'))
			->where('page_id = ' . (int) $iPageId . ' AND type_id = 0 AND user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRows');
		
		$aInvites = array();
		foreach ($aRows as $aRow)
		{
			$aInvites[$aRow['invited_user_id']] = $aRow;
		}
		
		return $aInvites;
	}

    public function isInvited($iPageId){
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(':pages_invite')
            ->where('page_id = ' . (int) $iPageId . ' AND type_id = 0 AND invited_user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');
        return ($iCnt) ? true : false;
    }

	public function getMembers($iPage, $iLimit = null)
	{
		if (!Phpfox::isModule('like'))
		{
			return false;
		}
		return Phpfox::getService('like')->getForMembers($this->getFacade()->getItemType(), $iPage, $iLimit);
	}
	
	public function getPerms($iPage)
	{
		switch($this->getFacade()->getItemType()) {
			case 'pages':
				$aCallbacks = Phpfox::massCallback('getPagePerms');
				break;

			case 'groups':
				$aCallbacks = Phpfox::massCallback('getGroupPerms');
				break;

			default:
				$aCallbacks = [];

		}
		$aPerms = array();
		$aUserPerms = $this->getPermsForPage($iPage);
		foreach ($aCallbacks as $aCallback)
		{
			foreach ($aCallback as $sId => $sPhrase)
			{
				$aPerms[] = array(
					'id' => $sId,
					'phrase' => $sPhrase,
					'is_active' => (isset($aUserPerms[$sId]) ? $aUserPerms[$sId] : '0')
				);	
			}			
		}	
		
		return $aPerms;
	}

	public function getPermsForPage($iPage)
	{
		static $aPerms = null;

		if (isset($aPerms[$iPage]) && is_array($aPerms[$iPage]))
		{
			return $aPerms[$iPage];
		}

		$aPerms[$iPage] = array();
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('pages_perm'))
			->where('page_id = ' . (int) $iPage)
			->execute('getSlaveRows');

		foreach ($aRows as $aRow)
		{
			$aPerms[$iPage][$aRow['var_name']] = (int) $aRow['var_value'];
		}

		return $aPerms[$iPage];
	}
	
	public function getPendingUsers($iPage)
	{
		$aUsers = $this->database()->select('ps.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('pages_signup'), 'ps')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
			->where('ps.page_id = ' . (int) $iPage)
			->execute('getSlaveRows');

		return $aUsers;
	}

	public function hasPerm($iPage = null, $sPerm)
	{
        if (Phpfox::isAdmin()){
            return true;
        }
		if (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getUserParam('core.can_view_private_items')) {
			return true;
		}

		if (defined('PHPFOX_POSTING_AS_PAGE')) {
			return true;
		}


		if ($iPage === null)
		{
			$iPage = $this->_aRow['page_id'];
		}
		$aPerms = $this->getPermsForPage($iPage);
		if (isset($aPerms[$sPerm]))
		{
			switch ((int) $aPerms[$sPerm])
			{
				case 1:
					if (!$this->isMember($iPage))
					{
						return false;
					}
					break;
				case 2:
					if (!$this->isAdmin($iPage))
					{
						return false;
					}
					break;
			}
		}
		return true;
	}
	
	public function getPendingTotal()
	{
		return (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('pages'))
			->where('app_id = 0 AND view_id = 1 AND item_type = ' . $this->getFacade()->getItemTypeId())
			->execute('getSlaveField');		
	}		
	
	public function getLastLogin()
	{
		static $aUser = null;
		
		if ($aUser !== null)
		{
			return $aUser;
		}
		
		$this->database()->join(Phpfox::getT('user'), 'u', 'u.user_id = pl.user_id');
		
		if (($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getlastlogin')))
		{
			eval($sPlugin);
		}		
		
		$aUser = $this->database()->select(Phpfox::getUserField() . ', u.email, u.style_id, u.password')
			->from(Phpfox::getT('pages_login'), 'pl')			
			->where('pl.login_id = ' . (int) Phpfox::getCookie('page_login') . ' AND pl.page_id = ' . Phpfox::getUserBy('profile_page_id'))
			->execute('getSlaveRow');
		
		if (!isset($aUser['user_id']))
		{
			$aUser = false;
			
			return false;
		}
		
		return $aUser;
	}
	
	public function getMyAdminPages($iLimit = 0)
	{
		$sCacheId = $this->cache()->set(array($this->getFacade()->getItemType(), Phpfox::getUserId()));

		if (!($aRows = $this->cache()->get($sCacheId)))
		{
				$iCntAdmins = $this->database()->select('COUNT(*)')
					->from(Phpfox::getT('pages_admin'), 'pa')
					->leftJoin(Phpfox::getT('pages'), 'pages', 'pages.page_id = pa.page_id')
					->where('pa.user_id = ' . Phpfox::getUserId())
					->execute('getSlaveField');		
				
			
				$this->database()->select('pages.*')
					->from(Phpfox::getT('pages'), 'pages')				
					->where('pages.app_id = 0 AND pages.view_id = 0 AND pages.user_id = ' . Phpfox::getUserId())							
					->union();		
	
	            if ($iCntAdmins > 0)
	            {
	                $this->database()->select('pages.*')
						->from(Phpfox::getT('pages_admin'), 'pa')
						->leftJoin(Phpfox::getT('pages'), 'pages', 'pages.page_id = pa.page_id')				
						->where('pa.user_id = ' . Phpfox::getUserId())							
						->union();
	            }					
				
				if ($iLimit > 0)
				{
					$this->database()->limit($iLimit);
				}
	
				$aRows = $this->database()->select('pages.*, pu.vanity_url, ' . Phpfox::getUserField())
					->unionFrom('pages')
					->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = pages.page_id')
					->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id')	
					->group('pages.page_id', true)
					->execute('getSlaveRows');	
	
				foreach ($aRows as $iKey => $aRow)
				{
					$aRows[$iKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
				}

				$this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group($this->getFacade()->getItemType(), $sCacheId);
		}
		
		if (!is_array($aRows))
		{
			$aRows = array();
		}		
		
		return $aRows;
	}


	public function getMyLoginPages()
	{
        $iCntAdmins = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages_admin'), 'pa')
            ->leftJoin(Phpfox::getT('pages'), 'pages', 'pages.page_id = pa.page_id')
            ->where('pa.user_id = ' . Phpfox::getUserId() . ' AND pages.item_type=0')
            ->execute('getSlaveField');

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages'))
            ->where('view_id = 0 AND app_id = 0 AND user_id = ' . Phpfox::getUserId() . ' AND item_type=0')
            ->execute('getSlaveField');

        $iCnt += $iCntAdmins;

        $this->database()->select('pages.*')
            ->from(Phpfox::getT('pages'), 'pages')
            ->where('pages.app_id = 0 AND pages.view_id = 0 AND pages.user_id = ' . Phpfox::getUserId(). ' AND pages.item_type=0')
            ->union();

        if ($iCntAdmins > 0) {
            $this->database()->select('pages.*')
                ->from(Phpfox::getT('pages_admin'), 'pa')
                ->leftJoin(Phpfox::getT('pages'), 'pages', 'pages.page_id = pa.page_id')
                ->where('pa.user_id = ' . Phpfox::getUserId() . ' AND pages.item_type=0')
                ->union();
        }


        $aRows = $this->database()->select('pages.*, pu.vanity_url, ' . Phpfox::getUserField())
            ->unionFrom('pages')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = pages.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id')
            ->group('pages.page_id, pu.vanity_url, u.user_id', true)
            ->order('pages.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['link'] = $this->getFacade()->getItems()->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
        }

		return array($iCnt, $aRows);
	}
	
	public function getClaims()
	{
		$aClaims = $this->database()->select('pc.*, u.full_name, u.user_name, p1.page_id, p1.title, curruser.user_id as curruser_user_id, curruser.full_name as curruser_full_name, curruser.user_name as curruser_user_name')
			->from(Phpfox::getT('pages_claim'), 'pc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = pc.user_id')			
			->join(Phpfox::getT('pages'), 'p1', 'p1.page_id = pc.page_id')
			->join(Phpfox::getT('user'), 'curruser', 'curruser.user_id = p1.user_id')
			->where('pc.status_id = 1')
			->order('pc.time_stamp')
			->execute('getSlaveRows');
		
		foreach ($aClaims as $iIndex => $aClaim)
		{
			$aClaims[$iIndex]['url'] = Phpfox::permalink($this->getFacade()->getItemType(), $aClaim['page_id'], $aClaim['title']);
		}
		return $aClaims;
	}
	
	public function getInfoForAction($aItem)
	{
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		$aRow = $this->database()->select('p.page_id, p.title, p.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('pages'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.page_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
        if (defined('PHPFOX_PAGES_ITEM_TYPE')){
            $sModule = PHPFOX_PAGES_ITEM_TYPE;
        } else {
            $sModule = 'pages';
        }
		$aRow['link'] = Phpfox_Url::instance()->permalink($sModule, $aRow['page_id'], $aRow['title']);
		return $aRow;
	}
	
	public function getPagesByLocation($fLat, $fLng)
	{
		$aPages = $this->database()->select('page_id, title, location_latitude, location_longitude, (3956 * 2 * ASIN(SQRT( POWER(SIN((' . $fLat . ' - location_latitude) *  pi()/180 / 2), 2) + COS(' . $fLat . ' * pi()/180) * COS(location_latitude * pi()/180) * POWER(SIN((' . $fLng . ' - location_longitude) * pi()/180 / 2), 2) ))) as distance')
		->from(Phpfox::getT('pages'))
		->having('distance < 1') // distance in kilometers
		->limit(10)
		->execute('getSlaveRows');
		
		return $aPages;
	}
	
	public function timelineEnabled($iId)
	{
	    return $this->database()->select('use_timeline')
		    ->from(Phpfox::getT('pages'))
		    ->where('page_id = ' . (int)$iId)
		    ->execute('getSlaveField');
	}
    
    /**
     * Gets the count of pages Without the pages created by apps. 
     * @param int $iUser
     * @return int
     */
    public function getPagesCount($iUser)
    {
		if ($iUser == Phpfox::getUserId())
		{
			return Phpfox::getUserBy('total_pages');
		}
		
        $iCount = $this->database()->select('count(*)')
                ->from(Phpfox::getT('pages'))
                ->where('app_id = 0 AND user_id = ' . (int)$iUser . ' AND item_type = '. $this->getFacade()->getItemTypeId())
                ->execute('getSlaveField');
        
        return $iCount;
    }
    
    /**
     * @param int $iPageId
     *
     * @return string
     */
    public function getTitle($iPageId){
        $aPage = $this->getPage($iPageId);
        $sTitle = $aPage['title'];
        return $sTitle;
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
		if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages__call'))
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
     * Get number of items in a category
     * @param $iCategoryId
     * @param $bIsSub
     * @param $iItemType
     * @param int $iUserId
     * @param bool $bGetCount
     * @return array|int|string
     */
    public function getItemsByCategory($iCategoryId, $bIsSub, $iItemType, $iUserId = 0, $bGetCount = false)
    {
        if ($bGetCount) {
            $extra_conditions = 'pages.type_id = ' . (int)$iCategoryId . ($iUserId ? ' AND pages.user_id = ' . (int)$iUserId : '');
            Phpfox::getService('privacy')->buildPrivacy(
                array(
                    'count' => true,
                    'module_id' => $this->getFacade()->getItemType(),
                    'alias' => 'pages',
                    'field' => 'page_id',
                    'table' => Phpfox::getT('pages'),
                    'service' => $this->getFacade()->getItemType() . '.browse'
                ), 'pages.time_stamp DESC', 0, null, ' AND ' . $extra_conditions, false
            );

            return db()->executeField();
        } else {
            return db()->select('*')
                ->from(':pages')
                ->where("item_type = " . $iItemType . (($bIsSub) ? " AND category_id = $iCategoryId" : " AND type_id = $iCategoryId AND category_id = 0") . ($iUserId ? " AND user_id = $iUserId" : ""))
                ->executeRows();
        }
    }

    /**
     * Move items to another category
     * @param $iOldCategoryId
     * @param $iNewCategoryId
     * @param $bOldIsSub, true if old category is sub category
     * @param $bNewIsSub, true if new category is sub category
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
                'type_id' => $iNewCategoryId,
                'category_id' => 0
            ];
        }
        foreach ($aItems as $aItem) {
            db()->update(Phpfox::getT('pages'), $aUpdates, 'page_id = ' . $aItem['page_id']);
        }
    }
    /**
     * Get user id of page
     * @param $iPageId
     * @return int|string
     */
    public function getUserId($iPageId)
    {
        return db()->select('user_id')->from(':user')->where(['profile_page_id' => $iPageId])->executeField();
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

        return db()->select('p.*, pc.name as category, pu.vanity_url')
            ->from($this->_sTable, 'p')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(':pages_url', 'pu', 'p.page_id = pu.page_id')
            ->where("p.page_id != $iPageid AND p.type_id = $aPage[type_id]")
            ->order('rand()')
            ->executeRows();
    }

    /**
     * Get user_id of page owner
     * @param $iPageId
     * @return int|string
     */
    public function getPageOwnerId($iPageId)
    {
        return db()->select('user_id')->from(':pages')->where(['page_id' => $iPageId])->executeField();
    }
}
