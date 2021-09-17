<?php

if (Phpfox::isUser()) {
    $aRows[$iKey]['is_blocked'] = Phpfox::getService('user.block')->isBlocked($aRow['user_id'], Phpfox::getUserId());
}

if (!isset($aUser['is_featured'])) {
    $aRows[$iKey]['is_featured'] = Phpfox::getService('user')->isFeatured($aRow['user_id']);
}