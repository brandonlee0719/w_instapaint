<?php
if (Phpfox::getUserParam('v.pf_video_view')) {
    $video_phrases = [
        'video' => _p('Videos'),
        'say' => _p('say_something_about_this_video'),
        'uploading' => _p('Uploading...'),
        'no_friends_found' => _p('no_friends_found'),
        'share' => \_p('share')
    ];

    $sData .= '<script>var v_phrases = ' . json_encode($video_phrases) . ';</script>';
    if (Phpfox::isModule('v')) {
        $val = user('pf_video_share', 1);
        $val = ($val) ? 1 : 0;
        $bCanCheckIn = Phpfox::getService('v.video')->canCheckInInFeed();

        $sData .= '<script>window.can_post_video_on_profile = ' . $val . ';</script>';
        $sData .= '<script>window.can_checkin_in_video = ' . $bCanCheckIn . ';</script>';
    }
}
