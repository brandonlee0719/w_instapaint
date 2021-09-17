<?php

if (!empty($aVals['module_id']) && !empty($aVals['item_id']) && $aVals['module_id'] == 'pages' && Phpfox::isModule('pages')) {
    if (!Phpfox::getService('pages.process')->setCoverPhoto($aVals['item_id'], $iId, true)) {
        return $this->error(_p('Cannot set cover photo for this page.'));
    }
}
