<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'pages') {
    $sFeedTable = 'pages_feed';
} else {
    if ($iFeedId && isset($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'pages') {
        $sFeedTable = 'pages_feed';
    }
}
