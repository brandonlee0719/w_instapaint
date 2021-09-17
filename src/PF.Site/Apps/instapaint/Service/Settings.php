<?php

namespace Apps\Instapaint\Service;

class Settings extends \Phpfox_Service
{
    /**
     * Returns an array with the global settings defined by admins.
     *
     * @return array The array representing the settings
     */
    public function getSettings() {
        $settings = [];

        $expeditedMinDays = db()->select('value')
            ->from(':instapaint_setting')
            ->where(['name' => 'expedited_min_days'])
            ->executeRow();

        $settings['expedited_min_days'] = (int) $expeditedMinDays['value'];

        return $settings;
    }
}
