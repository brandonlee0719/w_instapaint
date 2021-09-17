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
 * @package 		Phpfox_Service
 * @version 		$Id: maintain.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Admincp_Service_Maintain_Maintain extends Phpfox_Service 
{
    
    /**
     * @param array  $aVals
     * @param string $iPage
     * @param string $iLimit
     *
     * @return int
     */
	public function reParseText($aVals, $iPage = '', $iLimit = '')
	{
		$sUpdateTable = null;
		if (is_array($aVals['table']))
		{
			$sUpdateTable = $aVals['table'][1];
			$aVals['table'] = $aVals['table'][0];
		}		
		
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT($aVals['table']))
			->execute('getSlaveField');	
		
		if ($iCnt)
		{	
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT($aVals['table']))
				->limit($iPage, $iLimit, $iCnt)
				->order($aVals['item_field'] . ' DESC')
				->execute('getSlaveRows');				
				
			foreach ($aRows as $aRow)
			{
				$aUpdate = array(
					$aVals['parsed'] => $this->preParse()->reversePrepare($aRow[$aVals['original']])
				);
				
				if ($sUpdateTable !== null)
				{
					$aVals['table'] = $sUpdateTable;
				}

				$this->database()->update(Phpfox::getT($aVals['table']), $aUpdate, $aVals['item_field'] . ' = ' . $aRow[$aVals['item_field']]);
			}
		}
		
		return $iCnt;
	}
    
    /**
     * @param array $aList
     *
     * @return bool
     */
	public function removeDuplicates(&$aList)
	{		
		$sWhere = '';
		foreach ($aList['search'] as $sKey)
		{
			$sWhere .= 'v.' . $sKey . ' = t.' . $sKey . ' AND ';
		}
		$sWhere = rtrim($sWhere, ' AND ');		
		
		if ($aList['table'] == 'module')
		{
			$aRows = $this->database()->select('t.' . implode(', t.', $aList['search']) . '')
				->from(Phpfox::getT($aList['table']), 't')
				->group('t.' . implode(', t.', $aList['search']) . '', true)
				->execute('getSlaveRows');
		}
		else 
		{
			$aRows = $this->database()->select('t.' . implode(', t.', $aList['search']) . ', COUNT(DISTINCT v.' . $aList['key']. ') AS total_count')
				->from(Phpfox::getT($aList['table']), 't')
				->innerJoin(Phpfox::getT($aList['table']), 'v', $sWhere)
				->group('t.' . implode(', t.', $aList['search']) . '')
				->having('total_count > 1')
				->execute('getSlaveRows');
		}
				
		foreach ($aRows as $aRow)
		{
			if ($aList['table'] == 'module')
			{
				$iTotal = $this->database()->select('COUNT(*)')
					->from(Phpfox::getT('module'))
					->where('module_id = \'' . $this->database()->escape($aRow['module_id']) . '\'')
					->execute('getSlaveField');
				
				if ($iTotal > 1)
				{
					$sFile = PHPFOX_DIR_MODULE . $aRow['module_id'] . PHPFOX_DIR_MODULE_XML . PHPFOX_DS . 'phpfox' . PHPFOX_XML_SUFFIX;			
					
					if (file_exists($sFile))
					{
						$aModule = Phpfox::getLib('xml.parser')->parse($sFile);				
						if (isset($aModule['data']))
						{	 	
							$this->database()->delete(Phpfox::getT($aList['table']), 'module_id = \'' . $this->database()->escape($aRow['module_id']) . '\'');
							$this->database()->insert(Phpfox::getT($aList['table']), array(
									'module_id' => $aModule['data']['module_id'],
									'product_id' => 'phpfox',
									'is_core' => $aModule['data']['is_core'],
									'is_active' => 1,
									'is_menu' => $aModule['data']['is_menu'],
									'menu' => $aModule['data']['menu'],
									'phrase_var_name' => $aModule['data']['phrase_var_name']
								)
							);
							$bOk = true;
						}	
					}
				}
				else 
				{
					$bOk = true;
				}
			}
			
			if (!isset($bOk))
			{
				$sDeleteWhere = '';
				foreach ($aList['search'] as $sKey)
				{
					$sDeleteWhere .= $sKey . ' = \'' . $this->database()->escape($aRow[$sKey]) . '\' AND ';
				}
				$sDeleteWhere = rtrim($sDeleteWhere, ' AND ');
								
				$this->database()->delete(Phpfox::getT($aList['table']), $sDeleteWhere, ($aRow['total_count'] - 1));
			}
		}		
			
		return true;
	}

	public function cronRemoveCache()
    {
        $iHourToRemoveCache = Phpfox::getParam('core.auto_clear_cache');
        if ($iHourToRemoveCache) {
            $sLastRemoveId = Phpfox::getLib('cache')->set('cache_auto_remove_last_run');
            $aLastRemove = Phpfox::getLib('cache')->get($sLastRemoveId);

            //The cache has not been initialized
            if ($aLastRemove === false) {
                Phpfox::getLib('cache')->save($sLastRemoveId, ['time' => PHPFOX_TIME]);
            }

            $iNextRemove = $aLastRemove['time'] + abs($iHourToRemoveCache * 3600);

            //Remove cache
            if ($iNextRemove <= PHPFOX_TIME) {
                Phpfox::getLib('cache')->remove();
                Phpfox::getLib('template.cache')->remove();
                Phpfox::getLib('cache')->removeStatic();
                Phpfox::getLib('cache')->save($sLastRemoveId, ['time' => PHPFOX_TIME]);
            }
        }
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
		if ($sPlugin = Phpfox_Plugin::get('admincp.service_maintain_maintain__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}