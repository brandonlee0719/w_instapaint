<?php
$profilePageId = \Phpfox::getService('user')->getUser(Phpfox::getUserId(), 'u.profile_page_id');
if (array_key_exists('callback_module', $aVals) &&
    $aVals['callback_module'] == 'pages' && Phpfox::isModule('notification') && !$profilePageId['profile_page_id']
) {

    $aPage = Phpfox::getService('pages')->getPage($aVals['callback_item_id']);
    if ($aPage) {
        $iLinkId = Phpfox::getService('link.process')->getInsertId();
        $sLinkUrl = Phpfox::getService('link')->getUrl($iLinkId);

        // get all admins (include owner), send email and notification
        $aAdmins = Phpfox::getService('pages')->getPageAdmins($aVals['callback_item_id']);
        foreach ($aAdmins as $aAdmin) {
            if (Phpfox::getUserId() == $aAdmin['user_id']) {
                continue;
            }

            if ($aPage['user_id'] == $aAdmin['user_id']) {
                $sSubjectPhrase = _p('full_name_posted_a_link_on_your_page_title', [
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $aPage['title']
                ]);
                $sMessagePhrase = _p('full_name_posted_a_link_on_your_page_link', [
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $aPage['title'],
                    'link' => $sLinkUrl
                ]);
            } else {
                $sSubjectPhrase = _p('full_name_posted_a_link_on_page_title', [
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $this->_aCallback['item_title']
                ]);
                $sMessagePhrase = _p('full_name_posted_a_link_on_page_link', [
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $aPage['title'],
                    'link' => $sLinkUrl
                ]);
            }

            Phpfox::getLib('mail')->to($aAdmin['user_id'])
                ->translated(true)
                ->subject($sSubjectPhrase)
                ->message($sMessagePhrase)
                ->notification('comment.add_new_comment')
                ->send();

            Phpfox::getService('notification.process')->add('pages_comment_link', $iLinkId, $aAdmin['user_id']);
        }
    }
}
