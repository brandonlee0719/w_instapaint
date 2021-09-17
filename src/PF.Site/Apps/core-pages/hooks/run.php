<?php
defined('PHPFOX') or exit('NO DICE!');

$iProfilePageId = Phpfox::getUserBy('profile_page_id');
if (!PHPFOX_IS_AJAX && $iProfilePageId > 0 && Phpfox::getLib('pages.facade')->getPageItemType($iProfilePageId) == 'pages') {
    $bSend = true;
    if (defined('PHPFOX_IS_PAGE_ADMIN')) {
        $bSend = false;
    } else {
        $aPage = Phpfox::getService('pages')->getPage(Phpfox::getUserBy('profile_page_id'));
        $sReq1 = Phpfox_Request::instance()->get('req1');

        if (!Phpfox::getService('pages')->isInPage()) {
            Phpfox_Url::instance()->forward(Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'],
                $aPage['vanity_url']));
        }
    }
}
