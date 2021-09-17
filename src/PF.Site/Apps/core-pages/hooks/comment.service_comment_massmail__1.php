<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW) {
    foreach ($aRows as &$aRow) {
        if ($iPageId = Phpfox::getService('user')->getUser($aRow['owner_user_id'], 'u.profile_page_id')) {
            $aRow['user_id'] = Phpfox::getService('pages')->getPageOwnerId($iPageId['profile_page_id']);
        }
    }
}
