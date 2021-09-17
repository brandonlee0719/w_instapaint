<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'pages' && $iId == 2) {
    $aBlocks[2][] = ['type_id' => 0, 'component' => 'pages.pending', 'params' => []];
}
