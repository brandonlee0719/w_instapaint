<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: process.class.php 4335 2012-06-25 14:51:10Z Miguel_Espinoza $
 */
class Admincp_Service_Menu_Process extends Phpfox_Service 
{

	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('menu');
	}

    /**
     * import menu from app object
     *
     * @param \Core\App\App $App
     * @return bool
     */
	public function importFromApp($App)
    {
        if (empty($App->menu)) {
            return true;
        }

        $iId = db()->select('menu_id')
            ->from(':menu')
            ->where('module_id= "' . $App->alias . '" AND m_connection="main" AND url_value="' . $App->menu->url . '"')
            ->executeField();

        $aParams = [
            'm_connection' => 'main',
            'module_id' => $App->alias,
            'product_id' => 'phpfox',
            'allow_all' => true,
            'mobile_icon' => (isset($App->menu->icon) ? $App->menu->icon : null),
            'url_value' => $App->menu->url
        ];
        $bAddPhrase = true;
        $bIsUpdate = false;
        if (!empty($App->menu->phrase_var_name)) {
            $aParams['var_name'] = $App->menu->phrase_var_name;
            $bAddPhrase = false;
        }
        else {
            $aAllLanguage = Phpfox::getService('language')->getAll();
            $aText = [];
            foreach ($aAllLanguage as $aLanguage) {
                $aText[$aLanguage['language_id']] = $App->menu->name;
            }
            $aParams['text'] = $aText;
        }
        if ($iId) {
            $aParams['menu_id'] = $iId;
            $bIsUpdate = true;
        }

        $this->add($aParams, $bIsUpdate, (!$bIsUpdate), $bAddPhrase);

        return true;
    }
    
    /**
     * @param array $aVals
     * @param bool  $bIsUpdate
     * @param bool  $bCheckDuplicate
     * @param bool  $bAddPhrase
     *
     * @return bool
     */
	public function add($aVals, $bIsUpdate = false, $bCheckDuplicate = false, $bAddPhrase = true)
	{
		//check for duplicate menu
		if (empty($aVals['m_connection'])) {
			return Phpfox_Error::set(_p('select_where_to_place_this_menu_dot'));
		}

		if ($bCheckDuplicate && !empty($aVals['url_value'])) {
			$aMenus = Phpfox::getService('admincp.menu')->get(array('AND menu.url_value = \''.$aVals['url_value'].'\' AND menu.m_connection = \''.$aVals['m_connection'].'\''));
			if (count($aMenus) > 0) return true;
		}

		if (empty($aVals['module_id']))
		{
			$aVals['module_id'] = 'core|core';
		}


		$aModule = explode('|', $aVals['module_id']);
		
		// Find the user groups we disallowed
        $aDisallow = [];
        $aUserGroups = Phpfox::getService('user.group')->get();
        if (!isset($aVals['allow_all'])) {
            if (isset($aVals['allow_access'])) {
                foreach ($aUserGroups as $aUserGroup) {
                    if (!in_array($aUserGroup['user_group_id'], $aVals['allow_access'])) {
                        $aDisallow[] = $aUserGroup['user_group_id'];
                    }
                }
            } else {
                foreach ($aUserGroups as $aUserGroup) {
                    $aDisallow[] = $aUserGroup['user_group_id'];
                }
            }
        }

		if (isset($aVals['var_name'])) {
			$sVarName = $aVals['var_name'];
		} else {
            $bAddPhrase = true;
            if ($bIsUpdate) {
                $aMenu = Phpfox::getService('admincp.menu')->getForEdit($aVals['menu_id']);
                $sVarName = $aMenu['var_name'];
            }
            else {
                foreach ($aVals['text'] as $iId => $sText)
                {
                    $sVarName =  $aModule[0] . '_' . Phpfox::getService('language.phrase.process')->prepare($sText);
                    break;
                }

                $sVarName = 'menu_' . (isset($sVarName) ? $sVarName : '') . '_' . md5($aVals['m_connection'] . PHPFOX_TIME);
            }

		}

	
		$aInsert = array(
			'page_id' => (isset($aVals['page_id']) ? (int) $aVals['page_id'] : 0),
			'm_connection' => strtolower($aVals['m_connection']),
			'module_id' => $aModule[0],
			'product_id' => $aVals['product_id'],			
			'is_active' => 1,
			'url_value' => $aVals['url_value'],
			'disallow_access' => (isset($aVals['disallow_access']))? null : (count($aDisallow) ? serialize($aDisallow) : null),
			'mobile_icon' => (empty($aVals['mobile_icon']) ? null : $aVals['mobile_icon']),
            'var_name' => $sVarName
		);
		
		if (preg_match('/child\|(.*)/i', $aVals['m_connection'], $aMatches))
		{
			if (isset($aMatches[1]))
			{
				$aInsert['m_connection'] = null;
				$aInsert['parent_id'] = $aMatches[1];
			}
		}
		else if ($aVals['m_connection'] == 'explore' || $aVals['m_connection'] == 'main')
		{
			$aInsert['parent_id'] = 0;
		}
		
		if ($bIsUpdate) {
			$this->database()->update($this->_sTable, $aInsert, 'menu_id = ' . (int) $aVals['menu_id']);
		}
		else 
		{
			// Get the last order number
			$iLastCount = $this->database()->select('ordering')
				->from($this->_sTable)
				->order('ordering DESC')
				->execute('getSlaveField');
			
			// Define some remaining vars we plan to insert
			$aInsert['ordering'] = (!empty($aVals['ordering'])) ? $aVals['ordering'] : ($iLastCount + 1);
			$aInsert['version_id'] = (!empty($aVals['version_id'])) ? $aVals['version_id'] : Phpfox::getId();
			
			// Insert into DB
			$this->database()->insert($this->_sTable, $aInsert);
		}

        // Add the new phrase
        if ($bAddPhrase) {
            Phpfox::getService('language.phrase.process')->add([
                    'var_name'   => $sVarName,
                    'text'       => $aVals['text'],
                    'update'     => $bIsUpdate
                ]
            );
        }

		// Clear the menu cache using the substr method, which will clear anything that has a "menu" prefix
		$this->cache()->remove();
		
		return true;
	}
    
    /**
     * @param int   $iId
     * @param array $aVals
     *
     * @return bool
     */
	public function update($iId, $aVals)
	{
		$aVals['menu_id'] = $iId;
		return $this->add($aVals, true);
	}
    
    /**
     * @param array $aVals
     *
     * @return bool
     */
	public function updateOrder($aVals)
	{
        foreach ($aVals as $iId => $aValue) {
            $this->database()->update($this->_sTable, [
                'is_active' => 1,
                'ordering'  => (int)$aValue['ordering'],
            ], 'menu_id = ' . (int)$iId);
        }
        
        Phpfox::getLib('cache')->removeGroup('theme');
        Phpfox::getLib('cache')->removeGroup('menu');

        return true;
    }
    
    /**
     * @param int  $iDeleteId
     * @param bool $bIsVar
     *
     * @return bool
     */
    public function delete($iDeleteId, $bIsVar = false)
	{
		$aVar = $this->database()->select('menu_id, module_id, var_name')
			->from($this->_sTable)
			->where(($bIsVar ? "url_value = '" . $this->database()->escape($iDeleteId) . "'" : 'menu_id = ' . (int) $iDeleteId))
			->execute('getSlaveRow');
			
		if (!isset($aVar['module_id']))
		{
			return false;
		}
		
		$this->database()->delete($this->_sTable, ($bIsVar ? "url_value = '" . $this->database()->escape($iDeleteId) . "'" : 'menu_id = ' . (int) $iDeleteId));
		$this->database()->delete($this->_sTable, 'parent_id = ' . $aVar['menu_id']);
        //Check other menu still using this phrase
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('var_name="' . $aVar['var_name'] . '"')
            ->executeField();
        if (!$iCnt){
            Phpfox::getService('language.phrase.process')->delete($aVar['module_id'] . '.' . $aVar['var_name'], true);
            // Clear language cache
            Phpfox::getLib('cache')->removeGroup('locale');
            Phpfox::getLib('cache')->removeGroup('language');
        }

		// Clear menu cache
		$this->cache()->remove();

		return true;
	}
    
    /**
     * @param array $aVals
     * @param bool  $bMissingOnly
     *
     * @return bool
     */
	public function import($aVals, $bMissingOnly = false)
	{
		$iProductId = Phpfox::getService('admincp.product')->getId($aVals['product']);
		
		$aCache = array();
		if ($bMissingOnly)
		{
			$aRows = $this->database()->select('var_name')
				->from($this->_sTable)
				->execute('getRows', array(
						'free_result' => true
					)
				);
			foreach ($aRows as $aRow)
			{
				$aCache[$aRow['var_name']] = $aRow['var_name'];
			}
		}
		
		$aSql = array();
		$aVals = (isset($aVals['menu'][0]) ? $aVals['menu'] : array($aVals['menu']));
		foreach ($aVals as $aVal)
		{
			if ($bMissingOnly && in_array($aVal['var_name'], $aCache))
			{
				continue;
			}			
			
			$iModuleId = Phpfox_Module::instance()->getModuleId($aVal['module']);
			$aSql[] = array(	
				$aVal['parent_id'],
				$aVal['m_connection'],
				$iModuleId,
				$iProductId,
				$aVal['var_name'],
				1,
				$aVal['ordering'],
				$aVal['url_value'],
				(empty($aVal['disallow_access']) ? null : $aVal['disallow_access']),
				$aVal['version_id']
			);
		}
		
		if ($aSql)
		{
			$this->database()->multiInsert($this->_sTable, array(
				'parent_id',
				'm_connection',
				'module_id',
				'product_id',
				'var_name',
				'is_active',
				'ordering',
				'url_value',
				'disallow_access',
				'version_id'
			), $aSql);				
		}
		
		return true;
	}

    /**
     * @param int $iId
     * @param int $iType
     * @param boolean $bClearCache
     */
    public function updateActivity($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $aActive = [
            'is_active' => (int)($iType == '1' ? 1 : 0)
        ];

        $this->database()->update($this->_sTable, $aActive, 'menu_id= ' . (int) $iId);

        // Clear menu cache
        $this->cache()->remove();
    }
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
        if ($sPlugin = Phpfox_Plugin::get('admincp.service_menu_process__call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}