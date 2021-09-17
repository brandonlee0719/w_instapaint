<?php
defined('PHPFOX') or exit('NO DICE!');

$iProfilePageId = Phpfox::getUserBy('profile_page_id');
if (!PHPFOX_IS_AJAX && $iProfilePageId > 0 && Phpfox::getLib('pages.facade')->getPageItemType($iProfilePageId) == 'groups') {
    $bSend = true;
    $aPage = \Phpfox::getService('groups')->getPage($iProfilePageId);
    $sReq1 = Phpfox_Request::instance()->get('req1');
    if (defined('PHPFOX_IS_PAGE_ADMIN')) {
        $bSend = false;
    }

    if ($bSend && !\Phpfox::getService('groups')->isInPage()) {
        Phpfox_Url::instance()->forward(Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
            $aPage['vanity_url']));
    }
}
