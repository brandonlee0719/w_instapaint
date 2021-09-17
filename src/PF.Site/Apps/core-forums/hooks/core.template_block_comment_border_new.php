<?php
if ((Phpfox_Module::instance()->getFullControllerName() == 'forum.thread' || (PHPFOX_IS_AJAX && isset($_POST['core']) && $_POST['core']['call'] == 'forum.addReply')) && Phpfox::isUser()) {
    $aPost = $this->getVar('aPost');
    $aThread = (array)$this->getVar('aThread');
    $iTotalPosts = (int)$this->getVar('iTotalPosts');

    if ((!isset($aThread['forum_is_closed']) || !$aThread['forum_is_closed']) && Phpfox::getUserParam('forum.can_approve_forum_post') && $aPost['view_id'] == 1) {
        echo '<li><a href="#" onclick="$.ajaxCall(\'forum.approvePost\', \'detail=true&amp;post_id='.$aPost['post_id'].'\', \'GET\'); return false;"><i class="ico ico-check-square-alt"></i> '._p('approve').'</a></li>';
    }
    if ((!isset($aThread['forum_is_closed']) || !$aThread['forum_is_closed']) && ((user('forum.can_edit_own_post') && $aPost['user_id'] == Phpfox::getUserId())
        || user('forum.can_edit_other_posts')
        || Phpfox::getService('forum.moderate')->hasAccess($aPost['forum_id'], 'edit_post')
    )) {
        echo '<li><a href="#" onclick="$Core.box(\'forum.reply\', 800, \'id=' . $aPost['thread_id'] . '&amp;edit=' . $aPost['post_id'] . '\'); return false;"><i class="fa fa-pencil-square-o"></i> ' . _p('edit') . '</a></li>';

    }

    if (((!isset($aPost['is_started']) || !$aPost['is_started']) || $aPost['count'] > 0) && ((Phpfox::getUserParam('forum.can_delete_own_post') && $aPost['user_id'] == Phpfox::getUserId())
            || Phpfox::getUserParam('forum.can_delete_other_posts')
            || Phpfox::getService('forum.moderate')->hasAccess($aPost['forum_id'], 'delete_post')
            || (!empty($aThread['group_id']) && (Phpfox::isModule('pages') || Phpfox::isModule('groups')) && ($sModule = Phpfox::getPagesType($aThread['group_id'])) && Phpfox::isModule($sModule) && Phpfox::getService($sModule)->isAdmin($aThread['group_id']))
        )
    ) {
        echo '<li class="item_delete"><a href="#" onclick="return $Core.forum.deletePost(\'' . $aPost['post_id'] . '\');" title="' . _p('delete_this_post') . '"><i class="fa fa-times"></i> ' . _p('delete') . '</a></li>';

    }
}
