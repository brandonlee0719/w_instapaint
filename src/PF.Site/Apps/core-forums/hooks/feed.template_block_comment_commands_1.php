<?php
defined('PHPFOX') or exit('NO DICE!');

if ((Phpfox_Module::instance()->getFullControllerName() == 'forum.thread' || (PHPFOX_IS_AJAX && isset($_POST['core']) && $_POST['core']['call'] == 'forum.addReply')) && Phpfox::isUser()) {
    $aPost = $this->getVar('aPost');
    $aThread = (array)$this->getVar('aThread');
    $iTotalPosts = (int)$this->getVar('iTotalPosts');

    if (isset($aThread['canReply']) && $aThread['canReply']) {
        echo '<div class="forum_quote_holder feed-comment-link">
        <a role="button" class="forum_quote" onclick="$Core.box(\'forum.reply\', 800, \'id=' . $aPost['thread_id'] . '&amp;quote=' . $aPost['post_id'] . '&amp;total_post=' . $iTotalPosts . '\'); return false;"><span>' . _p('quote') . '</span></a></div>';
    }

    if (setting('forum.enable_thanks_on_posts') && (user('forum.can_thank_on_forum_posts')
            && ($aPost['user_id'] != Phpfox::getUserId()) && !Phpfox::getService('user.block')->isBlocked(null,
                $aPost['user_id']))
    ) {
        if (empty($aPost['thank_id'])) {
            echo '<div class="forum_thanks_holder feed-comment-link">
            <a role="button" id="forum_thanks_btn_' . $aPost['post_id'] . '" class="forum_thanks" onclick="$.ajaxCall(\'forum.thanks\', \'post_id=' . $aPost['post_id'] . '\');return false;" title="' . _p('thanks') . '"></a></div>';
        } else {
            echo '<div class="forum_thanks_holder feed-comment-link">
            <a role="button" id="forum_thanks_btn_' . $aPost['post_id'] . '" class="forum_thanks thanked" onclick="$.ajaxCall(\'forum.removeThanks\', \'thank_id=' . $aPost['thank_id'] . '\');return false;" title="' . _p('delete_thanks') . '"></a></div>';
        }
    }
}
