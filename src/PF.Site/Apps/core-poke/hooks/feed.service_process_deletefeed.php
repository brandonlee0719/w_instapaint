<?php
if ($aFeed['type_id'] == 'poke') {
    $aPokeData = Phpfox::getService('poke')->getPokeData($aFeed['item_id']);
    if (isset($aPokeData['user_id'])) {
        $aFeed['parent_user_id'] = $aPokeData['user_id'];
    }
}