<?php

namespace Apps\PHPfox_Videos\Installation\Version;

class v454
{
    public function process()
    {
        $aUpdateSettings = [
            'v.can_sponsor_v' => 'can_sponsor_v',
            'v.can_purchase_sponsor' => 'can_purchase_sponsor',
            'v.v_sponsor_price' => 'v_sponsor_price',
            'v.auto_publish_sponsored_item' => 'auto_publish_sponsored_item',
            'v.points_v' => 'points_v'
        ];

        foreach ($aUpdateSettings as $sOldSettingName => $sNewSettingName) {
            $iOldSettingId = db()->select('setting_id')->from(':user_group_setting')
                ->where(['module_id' => 'v', 'name' => $sOldSettingName])
                ->executeField();

            if (!$iOldSettingId) {
                continue;
            }

            $iNewSettingId = db()->select('setting_id')->from(':user_group_setting')
                ->where(['module_id' => 'v', 'name' => $sNewSettingName])
                ->executeField();

            // get old values and update to new settings
            $aOldValues = db()->select('user_group_id, value_actual')->from(':user_setting')->where(['setting_id' => $iOldSettingId])->executeRows();
            foreach ($aOldValues as $aValue) {
                db()->insert(':user_setting', [
                    'user_group_id' => $aValue['user_group_id'],
                    'setting_id' => $iNewSettingId,
                    'value_actual' => $aValue['value_actual']
                ]);
            }

            // delete old settings and it's values
            db()->delete(':user_group_setting', ['setting_id' => $iOldSettingId]);
            db()->delete(':user_setting', ['setting_id' => $iOldSettingId]);
        }

        db()->delete(\Phpfox::getT('menu'),
            "`module_id` = 'core' AND `var_name` like '%menu_core_videos_%' AND `url_value` = '/v'");
    }
}
