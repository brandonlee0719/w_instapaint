<?php
namespace Apps\phpFox_Shoutbox\Service;

use Apps\phpFox_Shoutbox\Service\Shoutbox as sb;
use Phpfox;

class Get
{
    private $_sTable;
    
    public function __construct()
    {
        $this->_sTable = sb::sbTable();
    }
    
    public function getShoutboxes($sModuleId = 'index', $iItemId = 0, $iLimit = 10)
    {
        if ($sModuleId == 'index') {
            $sExtra = '';
        } else {
            $sExtra = " AND parent_item_id=" . (int) $iItemId;
        }
        $aShoutboxes = db()
            ->select('s.*,' . Phpfox::getUserField('u'))
            ->from($this->_sTable, 's')
            ->join(':user', 'u', 'u.user_id=s.user_id')
            ->where("s.parent_module_id='" . $sModuleId . "'" . $sExtra)
            ->order("shoutbox_id DESC")
            ->limit($iLimit)
            ->execute('getSlaveRows');
        foreach ($aShoutboxes as $sKey => $aShoutbox) {
            if ($aShoutbox['user_id'] == Phpfox::getUserId()) {
                $aShoutboxes[$sKey]['type'] = 's';
            } else {
                $aShoutboxes[$sKey]['type'] = 'r';
            }
        }
        $aShoutboxes = array_reverse($aShoutboxes);
        return $aShoutboxes;
    }
    
    /**
     * @param int    $iShoutboxId
     * @param int    $iLimit
     * @param string $sModuleId
     * @param int    $iItemId
     *
     * @return array
     */
    public function getUpdateShoutboxes($iShoutboxId = 0, $iLimit = 30, $sModuleId = 'index', $iItemId = 0)
    {
        $sExtra = '';
        if ($sModuleId != 'index') {
            $sExtra = ' AND parent_item_id=' . (int)$iItemId;
        }
        $aShoutboxes = db()
            ->select('"r" AS type, s.*,' . Phpfox::getUserField('u'))
            ->from($this->_sTable, 's')
            ->join(':user', 'u', 'u.user_id=s.user_id')
            ->where('s.parent_module_id="' . $sModuleId . '" AND s.user_id !=' . (int)Phpfox::getUserId() . ' AND s.shoutbox_id >' . (int)$iShoutboxId . $sExtra)
            ->order("shoutbox_id DESC")
            ->limit($iLimit)
            ->execute('getSlaveRows');
        $aShoutboxes = array_reverse($aShoutboxes);
        return $aShoutboxes[0];
    }
    
    public function check($iShoutboxId = 0, $sModuleId = 'index', $iItemId = 0)
    {
        //check valid module_id
        $aValidModuleId = [
            'index',
            'pages',
            'groups'
        ];

        if (!in_array($sModuleId, $aValidModuleId)) {
            return [];
        }

        $sExtra = '';
        if ($sModuleId != 'index') {
            $sExtra = ' AND parent_item_id=' . (int)$iItemId;
        }
        $iCnt = db()
            ->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('parent_module_id="' . $sModuleId . '" AND user_id !=' . (int)Phpfox::getUserId() . ' AND shoutbox_id > ' . (int)$iShoutboxId . $sExtra)
            ->execute('getSlaveField');
        if ($iCnt) {
            return $this->getUpdateShoutboxes($iShoutboxId, $iCnt, $sModuleId, $iItemId);
        }
        return [];
    }

    public function PullingCheck($iShoutboxId = 0, $sModuleId = 'index', $iItemId = 0)
    {
        //check valid module_id
        $aValidModuleId = [
            'index',
            'pages',
            'groups'
        ];
        $iSleepTime = 2;
        $iMaxRequestTime = setting('shoutbox_polling_max_request_time');
        if ($iMaxRequestTime < 5 || $iMaxRequestTime > 30) {
            $iMaxRequestTime = 7;
        }

        if (!in_array($sModuleId, $aValidModuleId)) {
            return [];
        }

        $iSleptTime = 0;
        while (($iSleptTime < $iMaxRequestTime)) {
            $sExtra = '';
            if ($sModuleId != 'index') {
                $sExtra = ' AND parent_item_id=' . (int)$iItemId;
            }
            $iCnt = db()
                ->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('parent_module_id="' . $sModuleId . '" AND user_id !=' . (int)Phpfox::getUserId() . ' AND shoutbox_id > ' . (int)$iShoutboxId . $sExtra)
                ->execute('getSlaveField');
            if ($iCnt) {
                return $this->getUpdateShoutboxes($iShoutboxId, $iCnt, $sModuleId, $iItemId);
            } else {
                sleep($iSleepTime);
                $iSleptTime += $iSleepTime;
                continue;
            }
        }
        return [];
    }
    
    public function getLast($iShoutboxId = 0, $sModuleId = 'index', $iItemId = 0)
    {
        //check valid module_id
        $aValidModuleId = [
            'index',
            'pages',
            'groups'
        ];
        if (!in_array($sModuleId, $aValidModuleId)) {
            return [];
        }
        $sExtra = '';
        if ($sModuleId != 'index') {
            $sExtra = ' AND parent_item_id=' . (int)$iItemId;
        }
        $aShoutboxes = db()
            ->select('s.*,' . Phpfox::getUserField('u'))
            ->from($this->_sTable, 's')
            ->join(':user', 'u', 'u.user_id=s.user_id')
            ->where('s.parent_module_id="' . $sModuleId . '" AND s.shoutbox_id <' . (int)$iShoutboxId . $sExtra)
            ->order("shoutbox_id DESC")
            ->limit(30)
            ->execute('getSlaveRows');
        return $aShoutboxes;
    }
    
    /**
     * @param int $iShoutboxId
     *
     * @return array|bool
     */
    public function getShoutbox($iShoutboxId)
    {
        $aShoutbox = db()
            ->select('*')
            ->from($this->_sTable)
            ->where('shoutbox_id=' . (int)$iShoutboxId)
            ->execute('getSlaveRow');
        return isset($aShoutbox['shoutbox_id']) ? $aShoutbox : false;
    }
}