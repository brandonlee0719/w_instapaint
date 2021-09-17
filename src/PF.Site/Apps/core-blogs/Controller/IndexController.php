<?php

namespace Apps\Core_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class IndexController
 * @package Apps\Core_Blogs\Controller
 */
class IndexController extends Phpfox_Component
{
    const IMG_SUFFIX = '_500';

    /**
     * Controller
     */
    public function process()
    {
        $aParentModule = $this->getParam('aParentModule');
        $bIsAdmin = false;
        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {

            if (($this->request()->get('req1') == 'pages' && Phpfox::isModule('pages') == false) ||
                ($aParentModule['module_id'] == 'pages' && Phpfox::getService('pages')->hasPerm($aParentModule['item_id'],
                        'blog.view_browse_blog') == false)
            ) {
                return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
            }
            return Phpfox::getLib('module')->setController('blog.view');
        }

        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('blog_id', 'title'),
                    'table' => 'blog',
                    'redirect' => 'blog',
                    'title' => $sLegacyTitle,
                    'search' => 'title'
                )
            );
        }

        if ($this->request()->get('req2') == 'main') {
            return Phpfox::getLib('module')->setController('error.404');
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_start')) ? eval($sPlugin) : false);

        if (($iRedirectId = $this->request()->get('redirect')) && ($aRedirectBlog = Phpfox::getService('blog')->getBlogForEdit($iRedirectId))) {
            Phpfox::permalink('blog', $aRedirectBlog['blog_id'], $aRedirectBlog['title'], true);
        }

        Phpfox::getUserParam('blog.view_blogs', true);

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            $aUser = $this->getParam('aUser');
            if ($bIsProfile === true) {
                $this->search()->setCondition('AND blog.user_id = ' . $aUser['user_id']);
            }
        }

        /**
         * Check if we are going to view an actual blog instead of the blog index page.
         * The 2nd URL param needs to be numeric.
         */
        if (!Phpfox::isAdminPanel()) {
            if ($this->request()->getInt('req2') > 0 && !isset($aParentModule['module_id'])) {
                /**
                 * Since we are going to be viewing a blog lets reset the controller and get out of this one.
                 */
                return Phpfox::getLib('module')->setController('blog.view');
            }
        }

        /**
         * This creates a global variable that can be used in other components. This is a good way to
         * pass information to other components.
         */
        $this->setParam('sTagType', 'blog');
        $this->setParam('bShowHashTag', false);
        $this->setParam('sTagCloudHeader', _p('popular_topics'));

        $this->template()->setTitle(($bIsProfile ? _p('full_name_s_blogs',
            array('full_name' => $aUser['full_name'])) : _p('blog_title')))->setBreadCrumb(($bIsProfile ? _p('blogs') : _p('blog_title')),
            ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'blog') : $this->url()->makeUrl('blog')));

        $sView = $this->request()->get('view');

        $this->search()->set(array(
                'type' => 'blog',
                'field' => 'blog.blog_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'blog',
                    'search' => array(
                        'action' => ($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array('blog', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('blog',
                            array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'blog?view=' . $this->request()->get('view')),
                        'default_value' => _p('search_blogs_dot'),
                        'name' => 'search',
                        'field' => array('blog.title')
                    ),
                    'sort' => array(
                        'latest' => array('blog.time_stamp', _p('latest')),
                        'most-viewed' => array('blog.total_view', _p('most_viewed')),
                        'most-liked' => array('blog.total_like', _p('most_liked')),
                        'most-talked' => array('blog.total_comment', _p('most_discussed'))
                    ),
                    'show' => Phpfox::getUserParam('blog.total_blogs_displays', array(10, 20, 30))
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'blog',
            'alias' => 'blog',
            'field' => 'blog_id',
            'table' => Phpfox::getT('blog'),
            'hide_view' => array('pending', 'my')
        );

        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id'])) {
            $aFilterMenu = Phpfox::getService('blog')->getSectionMenu();
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_search')) ? eval($sPlugin) : false);

        $this->template()->buildSectionMenu('blog', $aFilterMenu);

        switch ($sView) {
            case 'spam':
                Phpfox::isUser(true);
                if (Phpfox::getUserParam('blog.can_approve_blogs')) {
                    $this->search()->setCondition('AND blog.is_approved = 9');
                }
                break;
            case 'pending':
                Phpfox::isUser(true);
                Phpfox::getUserParam('blog.can_approve_blogs', true);
                $this->search()->setCondition('AND blog.is_approved = 0');
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND blog.user_id = ' . Phpfox::getUserId());
                break;
            case 'draft':
                Phpfox::isUser(true);
                $this->search()->setCondition("AND blog.user_id = " . Phpfox::getUserId() . " AND blog.post_status = " . BLOG_STATUS_DRAFT);
                break;
            default:
                $aPage = $this->getParam('aPage');
                $sCondition = "AND blog.is_approved = 1 AND blog.post_status = 1" . (Phpfox::getUserParam('privacy.can_comment_on_all_items') ? "" : " AND blog.privacy IN(%PRIVACY%)");
                if (isset($aPage['privacy']) && $aPage['privacy'] == 1) {
                    $sCondition = "AND blog.is_approved = 1 AND blog.privacy IN(%PRIVACY%, 1) AND blog.post_status = 1";
                }
                $this->search()->setCondition($sCondition);

                http_cache()->set();

                break;
        }

        $bIsSearchTag = false;
        $aTag = null;

        if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aBlogCategory = Phpfox::getService('blog.category')->getCategory($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                if (!empty($aBlogCategory['parent_id']) && ($aBlogParentCategory = Phpfox::getService('blog.category')->getCategory($aBlogCategory['parent_id']))) {
                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aBlogParentCategory['name']),
                        Phpfox::permalink('blog.category', $aBlogParentCategory['category_id'],
                            $aBlogParentCategory['name']));
                    $this->setParam('iParentCategoryId', $aBlogCategory['parent_id']);
                }
                $this->search()->setCondition('AND blog_category.category_id = ' . $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')) . ' AND blog_category.user_id = ' . ($bIsProfile ? (int)$aUser['user_id'] : 0));

                $this->template()->setTitle(Phpfox::getSoftPhrase($aBlogCategory['name']));
                $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aBlogCategory['name']),
                    Phpfox::permalink('blog.category', $aBlogCategory['category_id'], $aBlogCategory['name']), true);

                $this->search()->setFormUrl($this->url()->permalink(array(
                    'blog.category',
                    'view' => $this->request()->get('view')
                ), $aBlogCategory['category_id'], $aBlogCategory['name']));

                $this->setParam(array(
                    'sCurrentCategory' => $aBlogCategory['category_id'],
                    'hasSubCategories' => true
                ));
            }
        } elseif (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support') && $this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req4' : ($bIsProfile === true ? 'req3' : 'req2'))) == 'tag') {
            $bIsSearchTag = true;
            if (!defined('PHPFOX_GET_FORCE_REQ')) {
                define('PHPFOX_GET_FORCE_REQ', true);
            }

            $sSearchTag = urldecode($this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req5' : ($bIsProfile === true ? 'req4' : 'req3'))));

            if ($aTag = Phpfox::getService('tag')->getTagInfo('blog', $sSearchTag)) {
                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\'');
            } else {
                $this->search()->setCondition('AND 0');
            }
        }
        if (isset($aParentModule) && isset($aParentModule['module_id'])) {
            /* Only get items without a parent (not belonging to pages) */
            $this->search()->setCondition('AND blog.module_id = \'' . $aParentModule['module_id'] . '\' AND blog.item_id = ' . (int)$aParentModule['item_id']);
            $this->template()->setTitle(_p('blog_title') . ' &raquo; ' . Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), true);
        } else {
            if ($aParentModule === null) {
                if (($sView == 'pending' && Phpfox::getUserParam('blog.can_approve_blogs')) || in_array($sView,
                        ['draft', 'my'])) {
                    $aModules = [];
                    if (!Phpfox::isModule('groups')) {
                        $aModules[] = 'groups';
                    }
                    if (!Phpfox::isModule('pages')) {
                        $aModules[] = 'pages';
                    }

                    if (count($aModules)) {
                        $this->search()->setCondition('AND blog.module_id NOT IN ("' . implode('","',
                                $aModules) . '")');
                    }
                } else {
                    $aModules = ['blog'];
                    // Apply setting show blog of pages / groups into All Blog
                    if (!defined('PHPFOX_IS_USER_PROFILE')) {
                        if (Phpfox::getParam('blog.display_blog_created_in_group') && Phpfox::isModule('groups')) {
                            $aModules[] = 'groups';
                        }

                        if (Phpfox::getParam('blog.display_blog_created_in_page') && Phpfox::isModule('pages')) {
                            $aModules[] = 'pages';
                        }
                    }

                    $this->search()->setCondition('AND blog.module_id IN ("' . implode('","', $aModules) . '")');
                }
            }
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                    'blog.view_browse_blogs'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aParentModule['module_id'] == 'pages' && Phpfox::getService('page')->hasPerm($aParentModule['item_id'],
                    'blog.view_browse_blogs')))
        ) {
            $sService = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
            if (Phpfox::getService($sService)->isAdmin($aParentModule['item_id'])) {
                $bIsAdmin = true;
                $this->request()->set('view', 'pages_admin');
            } elseif (Phpfox::getService($sService)->isMember($aParentModule['item_id'])) {
                $this->request()->set('view', 'pages_member');
            }
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('blog.blog_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();

        // Set pager
        $aParamsPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );

        Phpfox::getLib('pager')->set($aParamsPager);

        Phpfox::getService('blog')->getExtra($aItems, 'user_profile');

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_middle')) ? eval($sPlugin) : false);

        $this->template()->setMeta('keywords', Phpfox::getParam('blog.blog_meta_keywords'));
        $this->template()->setMeta('description', Phpfox::getParam('blog.blog_meta_description'));
        if ($bIsProfile) {
            $this->template()->setMeta('description',
                '' . $aUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' blogs.');
        }

        foreach ($aItems as &$aItem) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));
            if (!empty($aItem['tag_list'])) {
                $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
            }
        }
        /**
         * Here we assign the needed variables we plan on using in the template. This is used to pass
         * on any information that needs to be used with the specific template for this component.
         */
        $cnt = $this->search()->browse()->getCount();
        $this->template()->assign(array(
                'iCnt' => $cnt,
                'aBlogs' => $aItems,
                'sSearchBlock' => _p('search_blogs_'),
                'bIsProfile' => $bIsProfile,
                'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
                'sBlogStatus' => $this->request()->get('status'),
                'sView' => $sView,
                'iShorten' => BLOG_TEXT_SHORTEN
            )
        )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                )
            );

        $aModerationMenu = [];
        $bShowModerator = $bIsAdmin;

        if ($sView == 'pending' && Phpfox::getUserParam('blog.can_approve_blogs')) {
            $aModerationMenu[] = array(
                'phrase' => _p('approve'),
                'action' => 'approve'
            );
        }
        if (Phpfox::getUserParam('blog.can_feature_blog') && !in_array($sView, ['pending', 'draft'])) {
            $aModerationMenu[] = array(
                'phrase' => _p('Feature'),
                'action' => 'feature'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Unfeature'),
                'action' => 'unfeature'
            );
        }
        if (Phpfox::getUserParam('blog.delete_user_blog') || $bIsAdmin) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'blog',
                    'ajax' => 'blog.moderation',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }

        $this->template()->assign(array(
                'bShowModerator' => $bShowModerator
            )
        );

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE,
                    'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission',
                    $aParentModule['item_id'], 'blog.view_browse_blogs')) {
                $this->template()->assign(['aSearchTool' => []]);
                return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
            }

            if (!defined('PHPFOX_TAG_PARENT_MODULE')) {
                define('PHPFOX_TAG_PARENT_MODULE', PHPFOX_PAGES_ITEM_TYPE);
            }
            if (!defined('PHPFOX_TAG_PARENT_ID')) {
                define('PHPFOX_TAG_PARENT_ID', $aParentModule['item_id']);
            }
            $this->template()
                ->clearBreadCrumb()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']),
                    $aParentModule['url'])
                ->setBreadCrumb(_p('blogs'), $aParentModule['url'] . 'blog/');
        } elseif (Phpfox::getUserParam('blog.add_new_blog') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            sectionMenu(_p('add_new_blog'), 'blog/add');
        }

        if ($bIsSearchTag && !empty($aTag)) {
            $sExtra = '';
            if (defined('PHPFOX_TAG_PARENT_MODULE')) {
                $sExtra .= PHPFOX_TAG_PARENT_MODULE . '.' . PHPFOX_TAG_PARENT_ID . '.';
            }

            $this->template()->setBreadCrumb(_p('topic') . ': ' . $aTag['tag_text'] . '',
                $this->url()->makeUrl($sExtra . 'blog.tag') . $aTag['tag_url'] . '/', true);
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        $this->template()->clean(array(
                'iCnt',
                'aItems',
                'sSearchBlock'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
