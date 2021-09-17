<?php

namespace Apps\Core_RSS\Installation\Version;

use Phpfox_Plugin;
use Phpfox;

class v453
{
    public function __construct()
    {

    }

    public function process()
    {
        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps'], ['module_id' => 'rss']);

        $bAddFeedLink = db()->select('menu_id')
            ->from(':menu')
            ->where('m_connection = \'footer\' AND module_id = \'rss\' AND var_name = \'menu_rss\'')
            ->limit(1)
            ->executeField();

        if (empty($bAddFeedLink)) {
            db()->insert(':menu', array(
                'm_connection' => 'footer',
                'module_id' => 'rss',
                'product_id' => 'phpfox',
                'var_name' => 'menu_rss',
                'is_active' =>  1,
                'ordering' => 17,
                'url_value' => 'rss',
                'version_id' => '4.6.0'
            ));
        }
        //Import default rss feed of Core App
        $this->_importForumToRssFeed();
        $this->_importBlogToRssFeed();
        $this->_importEventToRssFeed();
        //Insert rss feed from other apps
        Phpfox::massCallback('processInstallRss');
    }
    private function _importForumToRssFeed()
    {
        $aRssGroup = [
            'module_id' => 'forum',
            'product_id' => 'phpfox',
            'name_var' => 'forum',
            'is_active' => 1,
            'ordering' => 0,
        ];
        $aRssData = [
            'module_id' => 'forum',
            'product_id' => 'phpfox',
            'feed_link' => 'forum',
            'php_view_code' => '$aRows = Phpfox::getService(\'forum.thread\')->getForRss(Phpfox::getParam(\'rss.total_rss_display\'));',
            'is_site_wide' => 1,
            'is_active' => 1
        ];
        $iCntRss = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'forum\'')
            ->execute('getSlaveField');
        if (!$iCntRss) {
            $iGroupId = db()->insert(':rss_group', $aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_' . $iGroupId, 'Forum');
            \Core\Lib::phrase()->addPhrase('rss_title_' . $iGroupId, 'Latest Forum Topics');
            \Core\Lib::phrase()->addPhrase('rss_description_' . $iGroupId,
                'List of the latest topics from our public forum.');
            db()->update(':rss_group', ['name_var' => 'forum.rss_group_name_' . $iGroupId],
                'group_id =' . $iGroupId);
            $aRssData['title_var'] = 'forum.rss_title_' . $iGroupId;
            $aRssData['description_var'] = 'forum.rss_description_' . $iGroupId;
            $aRssData['group_id'] = $iGroupId;
            db()->insert(':rss', $aRssData);
        } else {
            db()->update(':rss',
                ['php_view_code' => '$aRows = Phpfox::getService(\'forum.thread\')->getForRss(Phpfox::getParam(\'rss.total_rss_display\'));'],
                'module_id = \'forum\' AND php_view_code LIKE \'%$aRows = Forum_Service_Thread_Thread::instance()->getForRss%\'');
        }
    }
    private function _importBlogToRssFeed()
    {
        $aRssGroup = [
            'module_id' => 'blog',
            'product_id' => 'phpfox',
            'name_var' => 'blog',
            'is_active' => 1,
            'ordering' => 0,
        ];
        $aRssDatas = [
            [
                'module_id' => 'blog',
                'product_id' => 'phpfox',
                'feed_link' => 'blog',
                'php_view_code' => '$aRows = $this->database()->select(\'bt.text_parsed AS text, b.blog_id, b.title, u.user_name, u.full_name, b.time_stamp\')
                                        ->from(Phpfox::getT(\'blog\'), \'b\')
                                            ->join(Phpfox::getT(\'user\'), \'u\', \'u.user_id = b.user_id\')
                                        ->join(Phpfox::getT(\'blog_text\'), \'bt\',\'bt.blog_id = b.blog_id\')
                                        ->where(\'b.is_approved = 1 AND b.privacy = 0 AND b.post_status = 1\')
                                        ->limit(Phpfox::getParam(\'rss.total_rss_display\'))
                                        ->order(\'b.blog_id DESC\')
                                        ->execute(\'getSlaveRows\');
                                        $iCnt = count($aRows);
                                        
                                        foreach ($aRows as $iKey => $aRow)
                                        {
                                            $aRows[$iKey][\'description\'] = $aRow[\'text\'];
                                            $aRows[$iKey][\'link\'] = Phpfox::permaLink(\'blog\', $aRow[\'blog_id\'], $aRow[\'title\']);
                                            $aRows[$iKey][\'creator\'] = $aRow[\'full_name\'];
                                        }',
                'is_site_wide' => 1,
                'is_active' => 1,
                'title' => 'Latest Blogs',
                'description' => 'Latest Blogs'
            ],
            [
                'module_id' => 'blog',
                'product_id' => 'phpfox',
                'feed_link' => 'blog.category.{TITLE_URL}',
                'php_group_code' => '$aCategories = $this->database()->select(\'category_id, name\')
                                                ->from(Phpfox::getT(\'blog_category\'))
                                                ->where(\'user_id = 0\')
                                                ->execute(\'getSlaveRows\');
                                                if (count($aCategories))
                                                {
                                                    foreach ($aCategories as $aCategory)
                                                    {
                                                        $aRow[\'child\'][Phpfox::getLib(\'phpfox.url\')->makeUrl(\'rss\', array(\'id\' => $aRow[\'feed_id\'], \'category\' => $aCategory[\'category_id\']))] = $aCategory[\'name\'];
                                                    }
                                                }',
                'php_view_code' => 'list($iCnt, $aRows) = Phpfox::getService(\'blog.category\')->getBlogsByCategory(Phpfox::getLib(\'phpfox.request\')->get(\'category\'), 0, array(\'AND blog.is_approved = 1 AND blog.privacy = 0 AND blog.post_status = 1\'), \'blog.time_stamp DESC\', 0, Phpfox::getParam(\'rss.total_rss_display\'));

                                                foreach ($aRows as $iKey => $aRow)
                                                {
                                                    $aRows[$iKey][\'description\'] = $aRow[\'text\'];
                                                    $aRows[$iKey][\'link\'] = Phpfox::permalink(\'blog\', $aRow[\'blog_id\'], $aRow[\'title\']);
                                                    $aRows[$iKey][\'creator\'] = $aRow[\'full_name\'];
                                                }
                                                
                                                
                                                $aCategory = $this->database()->select(\'*\')
                                                    ->from(Phpfox::getT(\'blog_category\'))
                                                    ->where(\'category_id = \' . (int) Phpfox::getLib(\'phpfox.request\')->get(\'category\'))
                                                    ->execute(\'getSlaveRow\');
                                                
                                                $aFeed[\'feed_link\'] = Phpfox::permalink(\'blog.category\', $aCategory[\'category_id\'], $aCategory[\'name\']);
                                                $sDescription = $aCategory[\'name\'];',
                'is_active' => '1',
                'is_site_wide' => '0',
                'title' => 'Categories',
                'description' => 'Blog categories...'
            ]
        ];

        $iCntRss = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'blog\'')
            ->execute('getSlaveField');
        if(!$iCntRss)
        {
            $iGroupId = db()->insert(':rss_group',$aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_'.$iGroupId, 'Blogs');
            db()->update(':rss_group',['name_var' => 'blog.rss_group_name_'.$iGroupId],'group_id ='.$iGroupId);
            foreach ($aRssDatas as $aRssData) {
                \Core\Lib::phrase()->addPhrase('rss_title_' . $iGroupId, $aRssData['title']);
                \Core\Lib::phrase()->addPhrase('rss_description_' . $iGroupId, $aRssData['description']);
                unset($aRssData['title']);
                unset($aRssData['description']);
                $aRssData['title_var'] = 'blog.rss_title_' . $iGroupId;
                $aRssData['description_var'] = 'blog.rss_description_' . $iGroupId;
                $aRssData['group_id'] = $iGroupId;
                db()->insert(':rss', $aRssData);
            }
        }
    }
    private function _importEventToRssFeed()
    {
        $aRssGroup = [
            'module_id' => 'event',
            'product_id' => 'phpfox',
            'name_var' => 'event',
            'is_active' => 1,
            'ordering' => 0,
        ];
        $aRssData = [
            'module_id' => 'event',
            'product_id' => 'phpfox',
            'feed_link' => 'event',
            'php_view_code' => '$aRows = Phpfox::getService(\'event\')->getForRssFeed();',
            'is_site_wide' => 1,
            'is_active' => 1
        ];

        $iCntRss = db()->select('COUNT(*)')
            ->from(':rss_group')
            ->where('module_id = \'event\'')
            ->execute('getSlaveField');
        if(!$iCntRss)
        {
            $iGroupId = db()->insert(':rss_group',$aRssGroup);
            \Core\Lib::phrase()->addPhrase('rss_group_name_'.$iGroupId, 'Event');
            \Core\Lib::phrase()->addPhrase('rss_title_'.$iGroupId, 'Latest Events');
            \Core\Lib::phrase()->addPhrase('rss_description_'.$iGroupId, 'List of all the upcoming events.');
            db()->update(':rss_group',['name_var' => 'event.rss_group_name_'.$iGroupId],'group_id ='.$iGroupId);
            $aRssData['title_var'] = 'event.rss_title_'.$iGroupId;
            $aRssData['description_var'] = 'event.rss_description_'.$iGroupId;
            $aRssData['group_id'] = $iGroupId;
            db()->insert(':rss',$aRssData);
        }
        else {
            db()->update(':rss',['php_view_code' => '$aRows = Phpfox::getService(\'event\')->getForRssFeed();'],'module_id = \'event\' AND php_view_code = \'$aRows = Event_Service_Event::instance()->getForRssFeed();\'');
        }
    }
}
