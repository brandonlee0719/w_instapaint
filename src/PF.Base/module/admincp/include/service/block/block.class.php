<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Admincp
 * @version          $Id: block.class.php 7187 2014-03-13 18:43:36Z Fern $
 */
class Admincp_Service_Block_Block extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('block');
    }
    
    /**
     * @param null|string $mConnection
     * @param int  $iStyleId
     *
     * @return array|mixed
     */
    public function get($mConnection = null, $iStyleId = 0)
    {
        $sCacheId = $this->cache()->set('admincp_block_get_' . md5(($mConnection === null) ? 'all' : $mConnection) . $iStyleId);

        if (!$aRows = $this->cache()->get($sCacheId)) {
            (($sPlugin = Phpfox_Plugin::get('admincp.service_block_block_get')) ? eval($sPlugin) : false);

            if (($mConnection !== null && empty($mConnection)) || $mConnection == 'site_wide') {
                $this->database()
                    ->where('m_connection = \'\' OR ' . $this->database()
                            ->isNull('m_connection') . '');
            } else {
                if ($mConnection !== null) {
                    $this->database()
                        ->where('m_connection = \'' . $mConnection . '\'');
                }
            }

            $aGetRows = $this->database()
                ->select('b.*, m.module_id AS module_name')
                ->from($this->_sTable, 'b')
                ->leftJoin(Phpfox::getT('module'), 'm', 'm.module_id = b.module_id')
                ->execute('getSlaveRows');

            foreach ($aGetRows as $iKey => $aRow) {
                $sModule = explode('.', $aRow['m_connection']);
                $sModule = $sModule[0];

                if (!empty($sModule) && !Phpfox::isModule($sModule)) {
                    unset($aGetRows[$iKey]);
                }
            }

            $aStyleBlockCache = [];
            if ((int)$iStyleId > 0) {
                $aStyleBlockOrder = Phpfox_Database::instance()
                    ->select('*')
                    ->from(Phpfox::getT('block_order'))
                    ->where('style_id = ' . (int)$iStyleId)
                    ->execute('getSlaveRows');

                foreach ($aStyleBlockOrder as $aStyleBlock) {
                    $aStyleBlockCache[$aStyleBlock['block_id']] = $aStyleBlock['ordering'];
                }
            }

            $aSubRows = [];
            foreach ($aGetRows as $aGetRow) {
                $aSubRows[(int)(isset($aStyleBlockCache[$aGetRow['block_id']]) ? $aStyleBlockCache[$aGetRow['block_id']] : $aGetRow['ordering'])][] = $aGetRow;
            }

            ksort($aSubRows);

            $aRows = [];
            foreach ($aSubRows as $aOrdering) {
                foreach ($aOrdering as $iKey => $aRow) {
                    $aRows[] = $aRow;
                }
            }
            $aTemp = $aRows;
            foreach ($aRows as $iKey => $aRow) {
                $bSkip = false;
                foreach ($aTemp as $iCheck => $aCheck) {
                    if ($aCheck['block_id'] == $aRow['block_id'] && $iCheck != $iKey) {
                        unset($aRows[$iKey]);
                        $bSkip = true;
                        break;
                    }
                }
                if ($bSkip) {
                    continue;
                }

                if (Phpfox::getLib('parse.format')
                    ->isSerialized($aRow['location'])
                ) {
                    $aLocations = unserialize($aRow['location']);
                    $aRows[$iKey]['location'] = ((int)$iStyleId === 0 ? $aLocations['g'] : (isset($aLocations['s'][$iStyleId]) ? $aLocations['s'][$iStyleId] : $aLocations['g']));
                }

                $aRows[$iKey]['title'] = Phpfox_Locale::instance()
                    ->convert($aRow['title']);
            }

            $this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group('admincp', $sCacheId);
        }

        return $aRows;
    }
    
    /**
     * @param int $iId
     *
     * @return array
     */
    public function getForEdit($iId)
    {
        $sCacheId = $this->cache()->set('admincp_block_getForEdit_' . $iId);

        if (!$aRow = $this->cache()->get($sCacheId)) {
            (($sPlugin = Phpfox_Plugin::get('admincp.service_block_block_getforedit')) ? eval($sPlugin) : false);
    
            $aRow = $this->database()
                ->select('b.*, m.module_id AS module_name, bs.source_code')
                ->from($this->_sTable, 'b')
                ->leftJoin(Phpfox::getT('block_source'), 'bs', 'bs.block_id = b.block_id')
                ->leftJoin(Phpfox::getT('module'), 'm', 'm.module_id = b.module_id')
                ->where('b.block_id = ' . (int)$iId)
                ->execute('getSlaveRow');
    
            $aRow['component'] = ($aRow['module_id'] == '_app' ? $aRow['component'] : $aRow['module_name'] . '|' . $aRow['component']);
    
            if (Phpfox::getLib('parse.format')
                ->isSerialized($aRow['location'])
            ) {
                $aLocations = unserialize($aRow['location']);
                $aRow['style_id'] = $aLocations['s'];
                $aRow['location'] = $aLocations['g'];
            }
            $this->cache()->save($sCacheId, $aRow);
            Phpfox::getLib('cache')->group('admincp', $sCacheId);
        }
        return $aRow;
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
        if ($sPlugin = Phpfox_Plugin::get('admincp.service_block_block___call')) {
            return eval($sPlugin);
        }
        
        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
        return null;
    }
}