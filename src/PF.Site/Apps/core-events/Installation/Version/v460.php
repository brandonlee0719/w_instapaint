<?php

namespace Apps\Core_Events\Installation\Version;

use Core\Lib as Lib;
use Phpfox;

class v460
{

    private $_aEventCategories;
    private $_aRssData;
    private $_aRssGroup;

    public function __construct()
    {
        $this->_aEventCategories = array(
            'Arts',
            'Party',
            'Comedy',
            'Sports',
            'Music',
            'TV',
            'Movies',
            'Other',
        );
        $this->_aRssGroup = [
            'module_id' => 'event',
            'product_id' => 'phpfox',
            'name_var' => 'event',
            'is_active' => 1,
            'ordering' => 0,
        ];
        $this->_aRssData = [
            'module_id' => 'event',
            'product_id' => 'phpfox',
            'feed_link' => 'event',
            'php_view_code' => '$aRows = Phpfox::getService(\'event\')->getForRssFeed();',
            'is_site_wide' => 1,
            'is_active' => 1
        ];
    }

    public function process()
    {
        // add activity field
        if (!db()->isField(':user_activity', 'activity_event')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_event` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total blog field
        if (!db()->isField(':user_field', 'total_event')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_event` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }
        //add rss
        if(db()->tableExists(Phpfox::getT('rss_group')))
        {
            $this->_importToRssFeed();
        }
        // add default category
        $iTotalCategory = db()
            ->select('COUNT(category_id)')
            ->from(':event_category')
            ->execute('getField');
        if ($iTotalCategory == 0) {
            sort($this->_aEventCategories);
            $iCategoryOrder = 0;
            foreach ($this->_aEventCategories as $sCategory) {
                $iCategoryOrder++;
                db()->insert(':event_category', array(
                        'parent_id' => 0,
                        'name' => $sCategory,
                        'time_stamp' => PHPFOX_TIME,
                        'ordering' => $iCategoryOrder,
                        'is_active' => 1
                    )
                );
            }
        }
        // remove settings

        db()->delete(':setting', 'module_id="event" AND var_name="cache_events_per_user"');
        db()->delete(':setting', 'module_id="event" AND var_name="cache_upcoming_events_info"');
        db()->delete(':setting', 'module_id="event" AND var_name="event_basic_information_time_short"');
        db()->delete(':setting', 'module_id="event" AND var_name="event_view_time_stamp_profile"');
        db()->delete(':setting', 'module_id="event" AND var_name="event_browse_time_stamp"');
        db()->delete(':user_group_setting', 'module_id="event" AND name="can_view_pirvate_events"');
        //delete menu
        db()->delete(':menu', "`module_id` = 'event' AND `url_value` = 'event.add'");

        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'event']);
        //update location of block
        db()->update(':block', ['location' => 3],
            ['module_id' => 'event', 'm_connection' => 'event.view', 'component' => 'rsvp', 'location' => 1]);
        db()->delete(':block', ['module_id' => 'event', 'm_connection' => 'event.view', 'component' => 'attending']);
        db()->delete(':block', ['module_id' => 'event', 'm_connection' => 'event.view', 'component' => 'rsvp']);

    }
    public function importToRssFeed()
    {
        $this->_importToRssFeed();
    }

    private function _importToRssFeed()
    {
        $iCntRss = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'event\'')
            ->execute('getSlaveField');
        if(!$iCntRss)
        {
            $iGroupId = db()->insert(':rss_group',$this->_aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_'.$iGroupId, 'Event');
            \Core\Lib::phrase()->addPhrase('rss_title_'.$iGroupId, 'Latest Events');
            \Core\Lib::phrase()->addPhrase('rss_description_'.$iGroupId, 'List of all the upcoming events.');
            db()->update(':rss_group',['name_var' => 'event.rss_group_name_'.$iGroupId],'group_id ='.$iGroupId);
            $this->_aRssData['title_var'] = 'event.rss_title_'.$iGroupId;
            $this->_aRssData['description_var'] = 'event.rss_description_'.$iGroupId;
            $this->_aRssData['group_id'] = $iGroupId;
            db()->insert(':rss',$this->_aRssData);
        }
        else {
            db()->update(':rss',['php_view_code' => '$aRows = Phpfox::getService(\'event\')->getForRssFeed();'],'module_id = \'event\' AND php_view_code = \'$aRows = Event_Service_Event::instance()->getForRssFeed();\'');
        }
    }
}