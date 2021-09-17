<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Group_Process
 */
class User_Service_Group_Process extends Phpfox_Service 
{
    /**
     * @var string
     */
    protected $_sTable = '';
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user_group');
	}
	
	public function add($aVals)
	{		
		$iId = ($this->database()->select('user_group_id')
			->from($this->_sTable)
			->order('user_group_id DESC')
			->execute('getSlaveField') + 1);
		
		$aForm = array(
            'prefix' => array(
				'type' => 'string'
			),
			'suffix' => array(
				'type' => 'string'
			),
			'inherit_id' => array(
				'type' => 'int:required',
				'message' => _p('select_an_inherit_user_group')
			)
		);

		if (!defined('PHPFOX_INSTALLER')) {
		    //do not check phrase during installation
            $aPhraseVals = Phpfox::getService('language')->validateInput($aVals, 'title');
            $aLanguages = Phpfox::getService('language')->getAll();
            if ($aPhraseVals) {
                $phrase_var_name = 'user_group_title_' . md5('User Group Title' . PHPFOX_TIME . rand(1, 100000));
                //Add phrase
                $aText = [];
                foreach ($aLanguages as $aLanguage) {
                    if (!empty($aPhraseVals['title'][$aLanguage['language_id']])) {
                        $aText[$aLanguage['language_id']] = $aPhraseVals['title'][$aLanguage['language_id']];
                    }
                }
                $aValsPhrase = [
                    'var_name' => $phrase_var_name,
                    'text' => $aText
                ];
                $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
            }
        } else {
            $aForm['title'] = array(
                'type' => 'string'
            );
        }
		$aVals = $this->validator()->process($aForm, $aVals);

        if (!defined('PHPFOX_INSTALLER')) {
            if (isset($finalPhrase) && !empty($finalPhrase)) {
                $aVals['title'] = $finalPhrase;
            } else {
                //The phrase is not added
                return false;
            }
        }

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $aVals['user_group_id'] = $iId;

        if (isset($aVals['title'])) {
            $aVals['title'] = $this->preParse()->clean($aVals['title'], 255);
        }

        if (($aVals['icon_ext'] = $this->_uploadImage($iId)) === false) {
            return false;
        }

        if (is_bool($aVals['icon_ext'])) {
            $aVals['icon_ext'] = null;
        }
		
		$this->database()->insert($this->_sTable, $aVals);


        $aMenus = $this->database()->select('menu_id, disallow_access')
            ->from(Phpfox::getT('menu'))
            ->execute('getSlaveRows');

        $aCache = array();
        $aGroupRows = $this->database()->select('user_group_id')
            ->from($this->_sTable)
            ->execute('getSlaveRows');
        foreach ($aGroupRows as $aGroupRow) {
            $aCache[] = $aGroupRow['user_group_id'];
        }
		
		switch($aVals['inherit_id'])
		{
			case ADMIN_USER_ID:
				$sVar = 'default_admin';
				break;
			case GUEST_USER_ID:
				$sVar = 'default_guest';
				break;
			case STAFF_USER_ID:
				$sVar = 'default_staff';
				break;			
			case NORMAL_USER_ID:
				$sVar = 'default_user';
				break;
			default:
				break;
		}		
		
		if (empty($sVar))
		{
			$aGroupSettings = $this->database()->select('ugs.setting_id, ugc.module_id, ugc.name, ugc.default_value')
				->from(Phpfox::getT('user_group_custom'), 'ugc')
				->join(Phpfox::getT('user_group_setting'), 'ugs', 'ugc.name = ugs.name AND ugc.module_id = ugs.module_id')
				->where('ugc.user_group_id = ' . (int) $aVals['inherit_id'])
				->execute('getSlaveRows');
		}
		else 
		{
			$aGroupSettings = $this->database()->select('setting_id, module_id, name, ' . $sVar)	
				->from(Phpfox::getT('user_group_setting'))
				->execute('getSlaveRows');
		}

		$aActualSettings = $this->database()->select('setting_id, value_actual')
			->from(Phpfox::getT('user_setting'))
			->where('user_group_id = ' . (int) $aVals['inherit_id'])
			->execute('getSlaveRows');
			
		$aCacheSettings = array();
		foreach ($aActualSettings as $aActualSetting)
		{
			$aCacheSettings[$aActualSetting['setting_id']] = $aActualSetting['value_actual'];			
		}
		
		foreach ($aGroupSettings as $aGroupSetting)
		{	
			$sDefaultValue = (isset($aCacheSettings[$aGroupSetting['setting_id']]) ? $aCacheSettings[$aGroupSetting['setting_id']] : $aGroupSetting[(empty($sVar) ? 'default_value' : $sVar)]);
			if ($aGroupSetting['name'] == 'has_special_custom_fields')
			{
				$sDefaultValue = 0;
			}
			//Keep setting "has_special_custom_fields" and "custom_table_name"
            if ($aGroupSetting['module_id'] == 'custom'){
			    if ($aGroupSetting['name'] == 'has_special_custom_fields') {
			        continue;
                }
                if ($aGroupSetting['name'] == 'custom_table_name') {
			        continue;
                }
            }
			$this->database()->insert(Phpfox::getT('user_group_custom'), array(
					'user_group_id' => $iId,
					'module_id' => $aGroupSetting['module_id'],
					'name' => $aGroupSetting['name'],
					'default_value' => $sDefaultValue
				)
			);
		}		
		
		foreach ($aMenus as $aMenu)
		{
			if (empty($aMenu['disallow_access']))
			{
				continue;
			}
			
			$aGroups = unserialize($aMenu['disallow_access']);
			
			foreach ($aGroups as $iKey => $iGroup)
			{
				if (!in_array($iGroup, $aCache))
				{
					unset($aGroups[$iKey]);
				}
			}
			
			if (in_array($aVals['inherit_id'], $aGroups))
			{			
				array_push($aGroups, $iId);
				
				$this->database()->update(Phpfox::getT('menu'), array('disallow_access' => serialize($aGroups)), 'menu_id = ' . $aMenu['menu_id']);
			}
		}		
		
		$this->cache()->remove();
		
		return $iId;
	}
	
	public function delete($aVals)
	{
		$aGroup = Phpfox::getService('user.group')->getGroup($aVals['delete_id']);
		
		if (!isset($aGroup['user_group_id']))
		{
			return Phpfox_Error::display(_p('unable_to_find_the_user_group_you_want_to_delete'));
		}
		
		if ($aGroup['is_special'])
		{
			return Phpfox_Error::display(_p('not_allowed_to_delete_this_user_group'));
		}		
		
		$aMenus = $this->database()->select('menu_id, disallow_access')
			->from(Phpfox::getT('menu'))
			->execute('getSlaveRows');
		
		foreach ($aMenus as $aMenu)
		{
			if (empty($aMenu['disallow_access']))
			{
				continue;
			}
			
			$aGroups = unserialize($aMenu['disallow_access']);
			
			foreach ($aGroups as $iKey => $iGroup)
			{
				if ($iGroup == $aGroup['user_group_id'])
				{
					unset($aGroups[$iKey]);
				}
			}
			
			$this->database()->update(Phpfox::getT('menu'), array('disallow_access' => serialize($aGroups)), 'menu_id = ' . $aMenu['menu_id']);
		}		
		$bHasCustom = $this->database()->select('s.value_actual')
				->from(Phpfox::getT('user_setting'), 's')
				->join(Phpfox::getT('user_group_custom'), 'v', 'v.setting_id = s.setting_id')
				->where('s.user_group_id = ' . $aGroup['user_group_id'] . ' AND v.name= "has_special_custom_fields"')
				->execute('getSlaveField');
		if ($bHasCustom)
		{
			$sTableName = Phpfox::getParam(array('db','prefix')) . 'user_group_custom_' . $aGroup['user_group_id'];
			if ($this->database()->tableExists($sTableName) != false)
			{
				$this->database()->dropTables($sTableName);
			}
			if ($this->database()->tableExists($sTableName .'_value') != false)
			{
				$this->database()->dropTables($sTableName.'_value');
			}
		}
		$this->deleteIcon($aGroup['user_group_id']);
		
		$this->database()->delete(Phpfox::getT('user_group_custom'), 'user_group_id = ' . $aGroup['user_group_id']);
		$this->database()->delete(Phpfox::getT('user_setting'), 'user_group_id = ' . $aGroup['user_group_id']);
		$this->database()->delete(Phpfox::getT('user_group'), 'user_group_id = ' . $aGroup['user_group_id']);		
		$this->database()->update(Phpfox::getT('user'), array('user_group_id' => $aVals['user_group_id']), 'user_group_id = ' . $aGroup['user_group_id']);
		
		$this->database()->update(Phpfox::getT('subscribe_package'), array('is_active' => 0), 'user_group_id = ' . $aGroup['user_group_id']);
		$this->cache()->remove();
		
		return true;
	}
	
	public function update($iGroupId, $aVals)
	{
		$aForm = array(
			'prefix' => array(
				'type' => 'string'
			),
			'suffix' => array(
				'type' => 'string'
			)
		);

		//Validation language phrase

        $aPhraseVals = Phpfox::getService('language')->validateInput($aVals, 'title');
        $aLanguages = Phpfox::getService('language')->getAll();
        if ($aPhraseVals) {
            if (Core\Lib::phrase()->isPhrase($aVals['title_var_name'])){
                //Update phrase
                foreach ($aLanguages as $aLanguage){
                    if (isset($aVals['title'][$aLanguage['language_id']])){
                        $name = $aVals['title'][$aLanguage['language_id']];
                        Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['title_var_name'], $name);
                    }
                }
                $finalPhrase = $aVals['title_var_name'];
            } else {
                $phrase_var_name = 'user_group_title_' . md5('User Group Title' . PHPFOX_TIME . rand(1, 100000));
                //Add phrase
                $aText = [];
                foreach ($aLanguages as $aLanguage){
                    if (!empty($aVals['title'][$aLanguage['language_id']])){
                        $aText[$aLanguage['language_id']] = $aVals['title'][$aLanguage['language_id']];
                    }
                }
                $aValsPhrase = [
                    'var_name' => $phrase_var_name,
                    'text' => $aText
                ];
                $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
            }
        }

        $aVals = $this->validator()->process($aForm, $aVals);

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        if (($aVals['icon_ext'] = $this->_uploadImage($iGroupId)) === false) {
            return false;
        }

        if (is_bool($aVals['icon_ext'])) {
            $aVals['icon_ext'] = null;
        }
        if (isset($finalPhrase) && !empty($finalPhrase)) {
            $aVals['title'] = $finalPhrase;
        }
		$this->database()->update(Phpfox::getT('user_group'), $aVals, 'user_group_id = ' . (int) $iGroupId);
		
		$this->cache()->remove();
		
		return true;
	}	
	
	public function deleteIcon($iId)
	{
		$aGroup = Phpfox::getService('user.group')->getGroup($iId);
		if (!empty($aGroup['icon_ext']))
		{
			if (file_exists(Phpfox::getParam('core.dir_icon') . $aGroup['icon_ext']))
			{
				unlink(Phpfox::getParam('core.dir_icon') . $aGroup['icon_ext']);
			}
		}
			
		return true;		
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
		if ($sPlugin = Phpfox_Plugin::get('user.service_group_process__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
	private function _uploadImage($iId)
	{
		if (!empty($_FILES['icon']['name']))
		{
			$aImage = Phpfox_File::instance()->load('icon', array('jpg', 'gif', 'png'));
			if ($aImage === false)
			{
				return false;
			}
			
			$aGroup = Phpfox::getService('user.group')->getGroup($iId);
			if (!empty($aGroup['icon_ext']))
			{
				if (file_exists(Phpfox::getParam('core.dir_icon') . $aGroup['icon_ext']))
				{
					unlink(Phpfox::getParam('core.dir_icon') . $aGroup['icon_ext']);
				}
			}
			
			return Phpfox_File::instance()->upload('icon', Phpfox::getParam('core.dir_icon'), $iId, false, 0644, false);
		}		
		
		return true;
	}
}
