<?php
namespace Apps\phpFox_Shoutbox\Service;

use Phpfox;
use Apps\phpFox_Shoutbox\Service\Shoutbox as sb;

class Process
{
    private $_sTable = '';
    
    public function __construct()
    {
        $this->_sTable = sb::sbTable();
    }
    
    public function add($aVals)
    {
        Phpfox::isUser(true);
        $aValidField = [
            'parent_module_id' => 'index',
            'parent_item_id'   => 0,
            'user_id'          => Phpfox::getUserId(),
            'text'             => '',
            'timestamp'        => PHPFOX_TIME
        ];
        foreach ($aValidField as $sKey => $value) {
            if (in_array($sKey, ['user_id', 'timestamp'])) {
                //Do not allow change these fields from form
                continue;
            }
            if (isset($aVals[$sKey])) {
                $aValidField[$sKey] = $aVals[$sKey];
            }
        }
        //Begin check permission
        $bCanShare = user('shoutbox_can_share');
        if ($aValidField['parent_module_id'] == 'pages') {
            if (!setting('shoutbox_enable_pages')) {
                return false;
            }
            //In pages, check can share shoutbox
            if (!Phpfox::getService('pages')->hasPerm($aValidField['parent_item_id'], 'shoutbox.share_shoutbox')) {
                $bCanShare = false;
            }
        } elseif ($aValidField['parent_module_id'] == 'groups') {
            if (!setting('shoutbox_enable_groups')) {
                return false;
            }
            //In Groups, check can share shoutbox
            if (!Phpfox::getService('groups')->hasPerm($aValidField['parent_item_id'], 'shoutbox.share_shoutbox')) {
                $bCanShare = false;
            }
        }
        //end check permission
        if (!$bCanShare) {
            return false;
        }
        $iId = db()->insert(sb::sbTable(), $aValidField);
        return $iId;
    }
    
    public function delete($aShoutbox)
    {
        if (Phpfox::isAdmin() || $aShoutbox['user_id'] == Phpfox::getUserId()) {
            db()->delete($this->_sTable, 'shoutbox_id=' . (int) $aShoutbox['shoutbox_id']);
            return true;
        } else {
            return false;
        }
    }
}