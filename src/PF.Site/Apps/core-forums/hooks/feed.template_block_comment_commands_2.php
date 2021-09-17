<?php
defined('PHPFOX') or exit('NO DICE!');

if ((Phpfox_Module::instance()->getFullControllerName() == 'forum.thread') && Phpfox::getParam('forum.enable_thanks_on_posts')) {
    $aPost = $this->getVar('aPost');
    $aThread = (array)$this->getVar('aThread');
    $iTotalPosts = (int)$this->getVar('iTotalPosts');
    if (!isset($aPost['thanks_count'])) {
        $aPost['thanks_count'] = Phpfox::getService('forum.post')->getThanksCount($aPost['post_id']);
    }
    $sCountPhrase = _p('thanks_count', array('count' => $aPost['thanks_count']));

    echo '<div class="js_thank_post" ' . (($aPost['thanks_count'] == 0) ? 'style="display:none;" ' : '') . 'id="js_thank_' . $aPost['post_id'] . '"><a href="#" onclick="return $Core.box(\'forum.thanksBrowse\', \'\', \'post_id=' . $aPost['post_id'] . '\');">' . $sCountPhrase . '</a></div>';
}

