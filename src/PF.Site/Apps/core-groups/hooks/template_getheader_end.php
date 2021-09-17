<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE === 'groups') {
    if ($aIntegrate = storage()->get('groups_integrate')) {
        $aIntegrate = (array)$aIntegrate->value;
        if (array_key_exists('v', $aIntegrate) && !$aIntegrate['v']) {
            $sData .= '<script>if (typeof can_post_video_on_group === "undefined") {var can_post_video_on_group = 0} else {can_post_video_on_group = 0}</script>';
        }
    }
}
