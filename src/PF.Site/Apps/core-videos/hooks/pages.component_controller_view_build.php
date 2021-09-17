<?php
if (Phpfox::isModule('v') && Phpfox::getUserParam('v.pf_video_view')) {
    $val = user('pf_video_share', 1);
    if ($val) {
        $val = Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pf_video.share_videos');
    }
    $val = ($val) ? 1 : 0;
    $this->template()->setHeader('<script>window.can_post_video_on_page = ' . $val . ';</script>');
}
