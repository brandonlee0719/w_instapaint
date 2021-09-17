<?php
if (isset($this->_aCallback['module']) && $this->_aCallback['module'] == 'groups' && Phpfox::getUserId() != Phpfox::getService('groups')->getUserId($this->_aCallback['item_id'])) {
    $sLink = $this->_aCallback['link'] . 'comment-id_' . $iStatusId . '/';

    // get and send email/notification to all admins of group
    $aGroup = \Phpfox::getService('groups')->getPage($this->_aCallback['item_id']);
    $aAdmins = Phpfox::getService('groups')->getPageAdmins($this->_aCallback['item_id']);
    foreach ($aAdmins as $aAdmin) {
        if (Phpfox::getUserId() == $aAdmin['user_id']) {
            continue;
        }

        Phpfox::getLib('mail')->to($aAdmin['user_id'])
            ->translated(true)
            ->subject(_p('full_name_wrote_a_comment_on_group_title', [
                'full_name' => \Phpfox::getUserBy('full_name'),
                'title' => $aGroup['title']
            ]))
            ->message(_p('full_name_wrote_a_comment_on_group_link', [
                'full_name' => \Phpfox::getUserBy('full_name'),
                'title' => $aGroup['title'],
                'link' => $sLink
            ]))
            ->notification('comment.add_new_comment')
            ->send();
    }
}
