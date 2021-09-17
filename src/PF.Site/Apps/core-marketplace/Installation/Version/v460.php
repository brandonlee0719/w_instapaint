<?php

namespace Apps\Core_Marketplace\Installation\Version;

use Phpfox;

class v460
{

    private $_aMarketPlaceCategories;

    public function __construct()
    {
        $this->_aMarketPlaceCategories = array(
            'Community',
            'Houses',
            'Jobs',
            'Pets',
            'Rentals',
            'Services',
            'Stuff',
            'Tickets',
            'Vehicle'
        );
    }

    public function process()
    {
        // add activity field
        if (!db()->isField(':user_activity', 'activity_marketplace')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_marketplace` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total blog field
        if (!db()->isField(':user_field', 'total_listing')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_listing` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }
        // add default category
        $iTotalCategory = db()
            ->select('COUNT(category_id)')
            ->from(':marketplace_category')
            ->execute('getField');
        if ($iTotalCategory == 0) {
            foreach ($this->_aMarketPlaceCategories as $iCategoryOrder => $sCategory) {
                db()->insert(':marketplace_category', array(
                        'parent_id' => 0,
                        'name' => $sCategory,
                        'time_stamp' => PHPFOX_TIME,
                        'ordering' => $iCategoryOrder,
                        'used' => 0,
                        'is_active' => 1
                    )
                );
            }
        }

        // remove settings
        db()->delete(':setting', 'module_id="marketplace" AND var_name="marketplace_view_time_stamp"');
        // remove menu add
        db()->delete(':menu', "`module_id` = 'marketplace' AND `url_value` = 'marketplace.add'");

        db()->update(':module', ['phrase_var_name' => 'module_apps','is_active' => 1], ['module_id' => 'marketplace']);

        //Add cron
        $iCron = db()->select('COUNT(*)')
            ->from(':cron')
            ->where('module_id = \'marketplace\'')
            ->execute('getSlaveField');
        if (!$iCron) {
            db()->insert(Phpfox::getT('cron'), [
                'module_id' => 'marketplace',
                'product_id' => 'phpfox',
                'type_id' => 2,
                'every' => 1,
                'is_active' => 1,
                'php_code' => 'Phpfox::getService(\'marketplace.process\')->sendExpireNotifications();'
            ]);
        }
        // Update old settings
        $aSettingsMoreFrom = array(
            'limit' => Phpfox::getParam('marketplace.total_listing_more_from', 4),
            'cache_time' => Phpfox::getParam('core.cache_time_default'),
        );
        db()->update(':block', array('params' => json_encode($aSettingsMoreFrom)),
            'component = \'my\' AND module_id = \'marketplace\' AND params IS NULL');
        db()->delete(':setting', 'module_id="marketplace" AND var_name="total_listing_more_from"');

        $aSettingsSponsor = array(
            'limit' => Phpfox::getParam('marketplace.how_many_sponsored_listings', 4),
            'cache_time' => Phpfox::getParam('core.cache_time_default'),
        );
        db()->update(':block', array('params' => json_encode($aSettingsSponsor)),
            'component = \'sponsored\' AND module_id = \'marketplace\' AND params IS NULL');
        db()->delete(':setting', 'module_id="marketplace" AND var_name="how_many_sponsored_listings"');
    }
}