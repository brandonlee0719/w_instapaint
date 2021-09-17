<?php
if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE === 'groups') {
    foreach ($aLinks as $index => $aLink) {
        if ($aLink['module_id'] == 'photo' && $aIntegrates = storage()->get('groups_integrate')) {
            $aIntegrates = (array)$aIntegrates->value;
            if (array_key_exists('photo', $aIntegrates) && !$aIntegrates['photo']) {
                unset($aLinks[$index]);
            }
        }
    }
}
