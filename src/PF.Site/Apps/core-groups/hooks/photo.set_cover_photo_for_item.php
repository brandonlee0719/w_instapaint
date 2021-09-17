<?php

if (!empty($aVals['module_id']) && !empty($aVals['item_id']) && $aVals['module_id'] == 'groups' && Phpfox::isModule('groups')) {
    if (!Phpfox::getService('groups.process')->setCoverPhoto($aVals['item_id'], $iId, true)) {
        return $this->error(_p('Cannot set cover photo for this group.'));
    }
}
