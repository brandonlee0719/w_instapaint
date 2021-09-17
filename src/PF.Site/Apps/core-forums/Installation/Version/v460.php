<?php

namespace Apps\Core_Forums\Installation\Version;

use Phpfox;

class v460
{
    private $_aDefaultForums;
    private $_aRssData;
    private $_aRssGroup;

    public function __construct()
    {
        $this->_aDefaultForums = array(
            'Discussions' => array(
                'url' => 'discussions',
                'sub_forums' => array(
                    'General' => 'general',
                    'Movies' => 'movies',
                    'Music' => 'music'
                )
            ),
            'Computers & Technology' => array(
                'url' => 'computers-technology',
                'sub_forums' => array(
                    'Computers' => 'computers',
                    'Electronics' => 'electronics',
                    'Gadgets' => 'gadgets',
                    'General' => 'general'
                )
            )
        );
        $this->_aRssGroup = [
            'module_id' => 'forum',
            'product_id' => 'phpfox',
            'name_var' => 'forum',
            'is_active' => 1,
            'ordering' => 0,
        ];
        $this->_aRssData = [
            'module_id' => 'forum',
            'product_id' => 'phpfox',
            'feed_link' => 'forum',
            'php_view_code' => '$aRows = Phpfox::getService(\'forum.thread\')->getForRss(Phpfox::getParam(\'rss.total_rss_display\'));',
            'is_site_wide' => 1,
            'is_active' => 1
        ];
    }

    public function process()
    {
        //add data
        $iCntForum = db()->select('COUNT(*)')
            ->from(':forum')
            ->execute('getSlaveField');
        if (!$iCntForum) {
            $iCategoryOrder = 0;
            foreach ($this->_aDefaultForums as $sCategory => $aForum) {
                $iCategoryOrder++;
                $iForumId = db()->insert(':forum', array(
                        'is_category' => 1,
                        'name' => $sCategory,
                        'name_url' => $aForum['url'],
                        'ordering' => $iCategoryOrder
                    )
                );

                $iForumOrder = 0;
                foreach ($aForum['sub_forums'] as $sName => $sUrl) {
                    $iForumOrder++;
                    db()->insert(':forum', array(
                            'parent_id' => $iForumId,
                            'name' => $sName,
                            'name_url' => $sUrl,
                            'ordering' => $iForumOrder
                        )
                    );
                }

            }
        }
        if (db()->tableExists(Phpfox::getT('rss_group'))) {
            $this->_importToRssFeed();
        }

        // remove settings

        // Update old settings
        $aSettingsRecentPosts = array(
            'limit' => Phpfox::getParam('forum.total_recent_posts_display', 4),
        );
        db()->update(':block', array('params' => json_encode($aSettingsRecentPosts)),
            'component = \'recent-post\' AND module_id = \'forum\' AND params IS NULL');
        db()->delete(':setting', 'module_id=\'forum\' AND var_name=\'total_recent_posts_display\'');

        // Update old settings
        $aSettingsRecentThreads = array(
            'limit' => Phpfox::getParam('forum.total_recent_discussions_display', 4),
        );
        db()->update(':block', array('params' => json_encode($aSettingsRecentThreads)),
            'component = \'recent-thread\' AND module_id = \'forum\' AND params IS NULL');
        db()->delete(':setting', 'module_id=\'forum\' AND var_name=\'total_recent_discussions_display\'');

        db()->delete(':setting', 'module_id=\'forum\' AND var_name=\'forum_user_time_stamp\'');
        db()->delete(':user_group_setting', 'module_id=\'forum\' AND name=\'can_add_tags_on_threads\'');
        //delete menu
        db()->delete(':block', "`module_id` = 'forum' AND `component` = 'recent'");
        db()->delete(':component', "`module_id` = 'forum' AND `component` = 'recent'");

        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps'], ['module_id' => 'forum']);

    }
    public function importToRssFeed()
    {
        $this->_importToRssFeed();
    }

    private function _importToRssFeed()
    {
        $iCntRss = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'forum\'')
            ->execute('getSlaveField');
        if (!$iCntRss) {
            $iGroupId = db()->insert(':rss_group', $this->_aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_' . $iGroupId, 'Forum');
            \Core\Lib::phrase()->addPhrase('rss_title_' . $iGroupId, 'Latest Forum Topics');
            \Core\Lib::phrase()->addPhrase('rss_description_' . $iGroupId,
                'List of the latest topics from our public forum.');
            db()->update(':rss_group', ['name_var' => 'forum.rss_group_name_' . $iGroupId],
                'group_id =' . $iGroupId);
            $this->_aRssData['title_var'] = 'forum.rss_title_' . $iGroupId;
            $this->_aRssData['description_var'] = 'forum.rss_description_' . $iGroupId;
            $this->_aRssData['group_id'] = $iGroupId;
            db()->insert(':rss', $this->_aRssData);
        } else {
            db()->update(':rss',
                ['php_view_code' => '$aRows = Phpfox::getService(\'forum.thread\')->getForRss(Phpfox::getParam(\'rss.total_rss_display\'));'],
                'module_id = \'forum\' AND php_view_code LIKE \'%$aRows = Forum_Service_Thread_Thread::instance()->getForRss%\'');
        }
    }
}