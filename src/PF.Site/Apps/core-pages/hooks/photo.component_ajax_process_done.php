<?php
if ($aCallback['module'] == 'pages') {
    // temporary save content, because function send of mail clean all => cause issue when use plugin in ajax
    $content = ob_get_contents();
    ob_clean();

    // validate whom to send notification
    $aPage = Phpfox::getService('pages')->getPage($aPhoto['group_id']);
    $sLink = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);

    if ($aPhoto['user_id'] != $aPage['user_id'] &&
        $aPhoto['user_id'] != Phpfox::getService('pages')->getUserId($aPhoto['group_id'])
    ) {
        Phpfox::getLib('mail')->to($aPage['user_id'])
            ->translated(true)
            ->subject(_p('full_name_post_some_images_on_your_page_title', [
                'full_name' => Phpfox::getUserBy('full_name'),
                'title' => $aPage['title']
            ]))
            ->message(_p('full_name_post_some_images_on_your_page_title_link', [
                'full_name' => Phpfox::getUserBy('full_name'),
                'link' => $sLink,
                'title' => $aPage['title']
            ]))
            ->notification('comment.add_new_comment')
            ->send();

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('pages_post_image', $aPhoto['photo_id'], $aPage['user_id']);
        }
    }

    // get all admins and send notification
    $aAdmins = Phpfox::getService('pages')->getPageAdmins($aPage['page_id']);
    foreach ($aAdmins as $aAdmin) {
        if ($aAdmin['user_id'] == $aPhoto['user_id']) {
            continue;
        }

        Phpfox::getLib('mail')->to($aAdmin['user_id'])
            ->translated(true)
            ->subject(_p('full_name_post_some_images_on_page_title', [
                'full_name' => $aAdmin['full_name'],
                'title' => $aPage['title']
            ]))
            ->message(_p('full_name_post_some_images_on_page_title_link', [
                'full_name' => $aAdmin['full_name'],
                'link' => $sLink,
                'title' => $aPage['title']
            ]))
            ->notification('comment.add_new_comment')
            ->send();

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('pages_post_image', $aPhoto['photo_id'],
                $aAdmin['user_id']);
        }
    }

    // return content
    echo $content;
}
