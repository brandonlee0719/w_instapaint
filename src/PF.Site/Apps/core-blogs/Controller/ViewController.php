<?php

namespace Apps\Core_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class ViewController
 * @package Apps\Core_Blogs\Controller
 */
class ViewController extends Phpfox_Component
{
    const IMG_SUFFIX = '_1024';

    /**
     * Controller
     */
    public function process()
    {
        if ($this->request()->getInt('id')) {
            return Phpfox::getLib('module')->setController('error.404');
        }

        Phpfox::getUserParam('blog.view_blogs', true);

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_start')) ? eval($sPlugin) : false);

        $bIsProfile = $this->getParam('bIsProfile');
        if ($bIsProfile === true) {
            $this->setParam(array(
                    'bViewProfileBlog' => true,
                    'sTagType' => 'blog'
                )
            );
        }

        $aItem = Phpfox::getService('blog')->getBlog($this->request()->getInt('req2'));

        if (empty($aItem['blog_id'])) {
            return Phpfox_Error::display(_p('blog_not_found'));
        }

        if (isset($aItem['module_id']) && !empty($aItem['item_id']) && !Phpfox::isModule($aItem['module_id'])) {
            return Phpfox_Error::display(_p('Cannot find the parent item.'));
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aItem['user_id'])) {
            return Phpfox::getLib('module')->setController('error.invalid');
        }

        if (Phpfox::getUserId() == $aItem['user_id'] && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('blog_approved', $this->request()->getInt('req2'),
                Phpfox::getUserId());
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('blog', $aItem['blog_id'], $aItem['user_id'], $aItem['privacy'],
                $aItem['is_friend']);
        }

        if (isset($aItem['module_id']) && Phpfox::isModule($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'],
                'checkPermission')) {
            if (!Phpfox::callback($aItem['module_id'] . '.checkPermission', $aItem['item_id'],
                'blog.view_browse_blogs')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        if (!Phpfox::getUserParam('blog.can_approve_blogs')) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                return Phpfox_Error::display(_p('blog_not_found'), 404);
            }
        }

        if ($aItem['post_status'] == BLOG_STATUS_DRAFT && Phpfox::getUserId() != $aItem['user_id'] && !Phpfox::getUserParam('blog.edit_user_blog')) {
            return Phpfox_Error::display(_p('blog_not_found'));
        }

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aItem['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('blog', $aItem['blog_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('blog', $aItem['blog_id']);
                } else {
                    Phpfox::getService('track.process')->update('blog', $aItem['blog_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            Phpfox::getService('blog.process')->updateView($aItem['blog_id']);
            $aItem['total_view'] += 1;
        }


        // Define params for "review views" block
        $this->setParam(array(
                'sTrackType' => 'blog',
                'iTrackId' => $aItem['blog_id'],
                'iTrackUserId' => $aItem['user_id']
            )
        );

        $aCategories = Phpfox::getService('blog.category')->getCategoriesByBlogId($aItem['blog_id']);

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('blog', $aItem['blog_id']);
            if (isset($aTags[$aItem['blog_id']])) {
                $aItem['tag_list'] = $aTags[$aItem['blog_id']];
            }
        }

        $sCategories = '';
        if (isset($aCategories)) {
            $sCategories = '';
            foreach ($aCategories as $iKey => $aCategory) {
                $sCategories .= ($iKey != 0 ? ',' : '') . ' <a href="' . (isset($aCategory['user_id']) ? $this->url()->permalink($aItem['user_name'] . '.blog.category',
                        $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['category_name'])) : $this->url()->permalink('blog.category',
                        $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['category_name']))) . '">' . Phpfox::getSoftPhrase($aCategory['category_name']) . '</a>';

                $this->template()->setMeta('keywords', Phpfox::getSoftPhrase($aCategory['category_name']));
            }
        }

        $aItem['bookmark_url'] = Phpfox::permalink('blog', $aItem['blog_id'], $aItem['title']);

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_middle')) ? eval($sPlugin) : false);

        // Add topics to meta keywords
        if (!empty($aItem['tag_list']) && $aItem['tag_list']) {
            $this->template()->setMeta('keywords', Phpfox::getService('tag')->getForEdit('blog', $aItem['blog_id']));
        }

        $sBreadcrumb = $this->url()->makeUrl('blog');
        if (isset($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'getBlogDetails')) {
            if ($aCallback = Phpfox::callback($aItem['module_id'] . '.getBlogDetails', $aItem)) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
                $sBreadcrumb = $sBreadcrumb = $aCallback['url_home_photo'];
            }
        }

        $aParamFeed = array(
            'comment_type_id' => 'blog',
            'privacy' => $aItem['privacy'],
            'comment_privacy' => Phpfox::getUserParam('blog.can_post_comment_on_blog') ? $aItem['privacy_comment'] : DISABLE_COMMENT,
            'like_type_id' => 'blog',
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_is_friend' => $aItem['is_friend'],
            'item_id' => $aItem['blog_id'],
            'user_id' => $aItem['user_id'],
            'total_comment' => $aItem['total_comment'],
            'total_like' => $aItem['total_like'],
            'feed_link' => $aItem['bookmark_url'],
            'feed_title' => $aItem['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aItem['total_like'],
            'report_module' => 'blog',
            'report_phrase' => _p('report_this_blog'),
            'time_stamp' => $aItem['time_stamp']
        );

        //Disable like and comment if non-friend view profile blogs
        if (!Phpfox::getService('user.privacy')->hasAccess($aItem['user_id'], 'feed.share_on_wall')) {
            unset($aParamFeed['comment_type_id']);
            $aParamFeed['disable_like_function'] = true;
        }
        $this->setParam('aFeed', $aParamFeed);

        if (isset($aCallback) && isset($aCallback['module_id']) && $aCallback['module_id'] == 'pages') {
            $this->setParam('sTagListParentModule', $aItem['module_id']);
            $this->setParam('iTagListParentId', (int)$aItem['item_id']);
        }

        // Get Image
        if (!empty($aItem['image_path'])) {
            $aItem['image'] = Phpfox::getService('blog')->getImageUrl($aItem['image_path'], $aItem['server_id'],
                self::IMG_SUFFIX);
            $size_img = @getimagesize($aItem['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aItem['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }

        if ($aItem['is_featured']) {
            Phpfox::getLib('module')->appendPageClass('item-featured');
        }
        if ($aItem['is_sponsor']) {
            Phpfox::getLib('module')->appendPageClass('item-sponsor');
        }

        $this->setParam('aBlog', $aItem);

        // Retrieve permission on this blog
        Phpfox::getService('blog')->retrievePermission($aItem);

        $aTitleLabel = [
            'type_id' => 'blog'
        ];

        if ($aItem['is_featured']) {
            $aTitleLabel['label']['featured'] =[
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'diamond'

            ];
        }
        if ($aItem['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'sponsor'

            ];
        }
        if (!$aItem['is_approved']) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('this_blog_is_pending_an_admins_approval'),
                'actions' => []
            ];
            if ($aItem['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'blog.approve\', \'id='.$aItem['blog_id'].'\')'
                ];
            }
            if ($aItem['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('blog.add',['id' => $aItem['blog_id']]),
                ];
            }
            if ($aItem['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_ajax' => true,
                    'label' => _p('delete'),
                    'action' => '$Core.jsConfirm({}, function(){$.ajaxCall(\'blog.delete\', \'blog_id='.$aItem['blog_id'].'\');}, function(){})'
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        $this->template()->setTitle($aItem['title'])
            ->setBreadCrumb(_p('blogs_title'), $sBreadcrumb)
            ->setBreadCrumb($aItem['title'], $this->url()->permalink('blog', $aItem['blog_id'], $aItem['title']), true)
            ->setMeta('description', $aItem['title'] . '.')
            ->setMeta('description', $aItem['text'] . '.')
            ->setMeta('description', !empty($aItem['info']) ? $aItem['info'] . '.' : '')
            ->setMeta('keywords', $this->template()->getKeywords($aItem['title']))
            ->setMeta('keywords', Phpfox::getParam('blog.blog_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('blog.blog_meta_description'))
            ->assign(array(
                    'aItem' => $aItem,
                    'bBlogView' => true,
                    'bIsProfile' => $bIsProfile,
                    'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
                    'sMicroPropType' => 'BlogPosting',
                    'sCategories' => $sCategories,
                    'aTitleLabel' => $aTitleLabel,
                    'bIsDetail' => true
                )
            )->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                )
            );

        if ($this->request()->get('req4') == 'comment') {
            $this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\', 800); } }</script>');
        }

        if ($this->request()->get('req4') == 'add-comment') {
            $this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); } }</script>');
        }

        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id'])) {
            $aFilterMenu = Phpfox::getService('blog')->getSectionMenu();
        }

        $this->template()->buildSectionMenu('blog', $aFilterMenu);

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}
