<?php
if (defined('PHPFOX_IS_EVENT_VIEW')) {
    $sFeedTable = 'event_feed';
} else {
    if ($iFeedId && isset($aPhotoIte['module_id']) && $aPhotoIte['module_id'] == 'event') {
        $sFeedTable = 'event_feed';
    }
}