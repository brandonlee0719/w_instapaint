<?php
if (defined('PHPFOX_IS_PAGES_VIEW')) {
    foreach ($aLinks as $index => $aLink) {
        if ($aLink['module_id'] == 'photo' && $aIntegrates = storage()->get('pages_integrate')) {
            $aIntegrates = (array)$aIntegrates->value;
            if (array_key_exists('photo', $aIntegrates) && !$aIntegrates['photo']) {
                unset($aLinks[$index]);
            }
        }
    }
}
