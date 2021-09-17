<?php
namespace Apps\Core_Blogs\Installation\Version;

use Phpfox;
use \Core\Lib as Lib;

defined('PHPFOX') or exit('NO DICE!');
/**
 * Class v453
 * @package Apps\Core_Blogs\Installation\Version
 */
class v453
{
    private $_aBlogCategories;

    public function __construct()
    {
        $this->_aBlogCategories = array(
            'Business',
            'Education',
            'Entertainment',
            'Family & Home',
            'Health',
            'Recreation',
            'Shopping',
            'Society',
            'Sports',
            'Technology'
        );
    }


    public function process()
    {
        // add activity blog field
        if (!db()->isField(':user_activity', 'activity_blog')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_blog` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add user space for blog
        if (!db()->isField(':user_space', 'space_blog')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_space') . "` ADD `space_blog` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total blog field
        if (!db()->isField(':user_field', 'total_blog')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_blog` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add time stamp
        if (!db()->isField(':blog_category', 'time_stamp')) {
            if (db()->isField(':blog_category', 'added')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('blog_category') . "` CHANGE `added` `time_stamp` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
            } else {
                db()->query("ALTER TABLE  `" . Phpfox::getT('blog_category') . "` ADD `time_stamp` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
            }
        }

        // move module to app
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'blog']);

        // add default category
        $iTotalCategory = db()
            ->select('COUNT(category_id)')
            ->from(':blog_category')
            ->execute('getField');
        if ($iTotalCategory == 0) {
            foreach ($this->_aBlogCategories as $iCategoryOrder => $sCategory) {
                db()->insert(':blog_category', array(
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

        // Update old settings
        $aSettingsTopBloggers = array(
            'limit' => setting('top_bloggers_display_limit', 10),
            'cache' => setting('cache_top_bloggers', true),
            'cache_time' => setting('cache_top_bloggers_limit', 180),
            'display_blog_count' => setting('display_post_count_in_top_bloggers', true),
            'min_post' => setting('top_bloggers_min_post', 10)
        );
        db()->update(':block', array('params' => json_encode($aSettingsTopBloggers)),
            'component = \'top\' AND module_id = \'blog\' AND params IS NULL');

        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'blog_time_stamp\'');
        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'top_bloggers_display_limit\'');
        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'cache_top_bloggers\'');
        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'cache_top_bloggers_limit\'');
        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'display_post_count_in_top_bloggers\'');
        db()->delete(':setting', 'module_id=\'blog\' AND var_name=\'top_bloggers_min_post\'');
        db()->delete(':component', 'module_id=\'blog\' AND component=\'profile.index\'');
        db()->delete(':menu', 'module_id = \'blog\' AND var_name = \'menu_add_new_blog\'');

        //4.6.0
        db()->delete(':block', 'module_id = \'blog\' AND component = \'topic\'');

        $aMetaKeys = [
            'blog_meta_description' => array(
                'description' => '<title>Blog Meta Description</title><info>Meta description added to pages related to the Blog app. <a target="_bank" href="' . Phpfox::getLib('url')->makeUrl('admincp.language.phrase') . '?q=seo_blog_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_blog_meta_description"></span></info>',
                'meta_value' => 'Read up on the latest blogs on Site Name.'
            ),
            'blog_meta_keywords' => array(
                'description' => '<title>Blog Meta Description</title><info>Meta keywords that will be displayed on sections related to the Blog app. <a target="_bank" href="' . Phpfox::getLib('url')->makeUrl('admincp.language.phrase') . '?q=seo_blog_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_blog_meta_keywords"></span></info>',
                'meta_value' => 'blog, blogs, journals'
            ),
        ];
        // We'll update only one time
        foreach ($aMetaKeys as $sSetting => $aMetaKey) {
            $sNewPhrase = 'seo_' . $sSetting;
            if (!Lib::phrase()->isPhrase($sNewPhrase)) {
                $sValue = Phpfox::getParam('blog.' . $sSetting);

                // If old setting is formatted phrase already
                if (preg_match('/\{_p var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } elseif (preg_match('/\{phrase var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } else {
                    $sValue = $aMetaKey['meta_value'];
                    Lib::phrase()->addPhrase($sNewPhrase, $sValue);
                }

                // Update setting value
                db()->update(':setting', array(
                    'value_actual' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'value_default' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'type_id' => ''
                ), 'module_id=\'blog\' AND var_name=\'' . $sSetting . '\'');

                // Update setting description
                db()->update(':language_phrase', array(
                    'text' => $aMetaKey['description'],
                    'text_default' => $aMetaKey['description']
                ), 'var_name = \'setting_' . $sSetting . '\'');
            }
        }

        // Import to rss feed
        if (db()->tableExists('rss') && db()->tableExists('rss_group')) {
            $this->_importToRssFeed();
        }
    }

    public function importToRssFeed()
    {
        $this->_importToRssFeed();
    }

    private function _importToRssFeed()
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
}
