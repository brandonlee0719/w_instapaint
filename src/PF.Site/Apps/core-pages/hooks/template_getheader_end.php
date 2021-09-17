<?php
if (defined('PHPFOX_IS_PAGES_VIEW')) {
    if ($aIntegrate = storage()->get('pages_integrate')) {
        $aIntegrate = (array)$aIntegrate->value;
        if (array_key_exists('v', $aIntegrate) && !$aIntegrate['v']) {
            $sData .= '<script>if (typeof can_post_video_on_page === "undefined") {var can_post_video_on_page = 0} else {can_post_video_on_page = 0}</script>';
        }
    }
}
