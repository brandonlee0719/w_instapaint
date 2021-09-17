<?php

namespace Apps\Core_Blogs\Service;

use Core;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Callback
 * @package Apps\Core_Blogs\Service
 */
class Callback extends Phpfox_Service
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog');
    }

    /**
     * @param int $iItemId
     *
     * @return array
     */
    public function getFeedDetails($iItemId)
    {
        return [
            'module' => 'blog',
            'item_id' => $iItemId
        ];
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('blog')->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_blogs', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => \Phpfox_Url::instance()->makeUrl('blog', array('view' => 'pending'))
        ];
    }

    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'is_approved = 1 AND post_status = ' . BLOG_STATUS_PUBLIC;
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . db()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . db()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'blog.blogs',
            'total' => $iCnt
        ];
    }

    /**
     * Used for the function core.callback::getRedirection
     * @return string
     */
    public function getRedirectionTable()
    {
        return Phpfox::getT('blog_redirect');
    }

    /**
     * @param string $sTag
     * @param array $aConds
     * @param string $sSort
     * @param string $iPage
     * @param string $sLimit
     *
     * @return array
     */
    public function getTags($sTag, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettags__start')) ? eval($sPlugin) : false);
        $aBlogs = array();
        $iCnt = db()->select('COUNT(*)')
            ->from(Phpfox::getT('blog'), 'blog')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
            ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
            ->where($aConds)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aRows = db()->select("blog.*, " . (Phpfox::getParam('core.allow_html') ? "blog_text.text_parsed" : "blog_text.text") . " AS text, " . Phpfox::getUserField())
                ->from(Phpfox::getT('blog'), 'blog')
                ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
                ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
                ->join(Phpfox::getT('user'), 'u', 'blog.user_id = u.user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)
                ->execute('getSlaveRows');

            if (count($aRows)) {
                foreach ($aRows as $aRow) {
                    $aBlogs[$aRow['blog_id']] = $aRow;
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettags__end')) ? eval($sPlugin) : false);
        return array($iCnt, $aBlogs);
    }

    /**
     * @param array $aConds
     * @param string $sSort
     *
     * @return array
     */
    public function getTagSearch($aConds = array(), $sSort)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettagsearch__start')) ? eval($sPlugin) : false);
        $aRows = db()->select("blog.blog_id AS id")
            ->from(Phpfox::getT('blog'), 'blog')
            ->innerJoin(Phpfox::getT('tag'), 'tag', "tag.item_id = blog.blog_id")
            ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
            ->where($aConds)
            ->group('blog.blog_id', true)
            ->order($sSort)
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aRows as $aRow) {
            $aSearchIds[] = $aRow['id'];
        }
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettagsearch__end')) ? eval($sPlugin) : false);
        return $aSearchIds;
    }

    /**
     * @return array
     */
    public function getTagCloud()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettagcloud__start')) ? eval($sPlugin) : false);
        return array(
            'link' => 'blog',
            'category' => 'blog'
        );
    }

    /**
     * @param array $aRow
     *
     * @return array|bool
     */
    public function getActivityFeedComment($aRow)
    {
        if (Phpfox::isUser() && Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aItem = db()->select('b.blog_id, b.title, b.time_stamp, b.total_comment, b.total_like, c.total_like, ct.text_parsed AS text, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('blog'), 'b', 'c.type_id = \'blog\' AND c.item_id = b.blog_id AND c.view_id = 0')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('c.comment_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aItem['blog_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink('blog', $aItem['blog_id'], $aItem['title']);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aItem['title'],
            (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : 50));
        $sUser = '<a href="' . Phpfox::getLib('url')->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
        $sGender = Phpfox::getService('user')->gender($aItem['gender'], 1);

        if ($aRow['user_id'] == $aItem['user_id']) {
            $sMessage = _p('posted_a_comment_on_gender_blog_a_href_link_title_a',
                array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
        } else {
            $sMessage = _p('posted_a_comment_on_user_name_s_blog_a_href_link_title_a',
                array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
        }
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getactivityfeedcomment__1')) ? eval($sPlugin) : false);

        return array(
            'no_share' => true,
            'feed_info' => $sMessage,
            'feed_link' => $sLink,
            'feed_status' => $aItem['text'],
            'feed_total_like' => $aItem['total_like'],
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/blog.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'like_type_id' => 'feed_mini'
        );
    }

    /**
     *
     */
    public function canShareItemOnFeed()
    {
    }

    /**
     * @param array $aRow
     *
     * @return bool|array
     */
    public function getActivityFeedCustomChecks($aRow)
    {
        if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                    'blog.view_browse_blogs'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['custom_data_cache']['item_id'],
                    'blog.view_browse_blogs'))
        ) {
            return false;
        }

        return $aRow;
    }

    /**
     * @param array $aRow
     * @param null $aCallback
     * @param bool $bIsChildItem
     *
     * @return array|bool
     */
    public function getActivityFeed($aRow, $aCallback = null, $bIsChildItem = false)
    {
        if (!Phpfox::getUserParam('blog.view_blogs')) {
            return false;
        }

        if (Phpfox::isUser()) {
            db()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'blog\' AND l.item_id = b.blog_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($bIsChildItem) {
            db()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = b.user_id');
        }

        $aBlog = db()->select('b.user_id, b.blog_id, b.title, b.time_stamp, b.total_comment, b.image_path, b.server_id, b.privacy, b.total_like, bt.text_parsed AS text, b.module_id, b.item_id, b.total_view')
            ->from(Phpfox::getT('blog'), 'b')
            ->join(Phpfox::getT('blog_text'), 'bt', 'bt.blog_id = b.blog_id')
            ->where('b.blog_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return false;
        }

        /**
         * Check active parent module
         */
        if (!empty($aBlog['module_id']) && !Phpfox::isModule($aBlog['module_id'])) {
            return false;
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                        'blog.view_browse_blogs'))
                || (!defined('PHPFOX_IS_PAGES_VIEW') && $aBlog['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aBlog['item_id'],
                        'blog.view_browse_blogs')))
            || ($aBlog['module_id'] && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'],
                    'canShareOnMainFeed') && !Phpfox::callback($aBlog['module_id'] . '.canShareOnMainFeed',
                    $aBlog['item_id'], 'blog.view_browse_blogs', $bIsChildItem))
        ) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        $aBlog['group_id'] = $aBlog['item_id'];
        $aRow['item_id'] = $aBlog['blog_id'];
        $aReturn = array_merge(array(
            'feed_title' => $aBlog['title'],
            'privacy' => $aBlog['privacy'],
            'feed_info' => _p('posted_a_blog'),
            'feed_link' => Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']),
            'total_comment' => $aBlog['total_comment'],
            'feed_total_like' => $aBlog['total_like'],
            'feed_is_liked' => isset($aBlog['is_liked']) ? $aBlog['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/blog.png',
                'return_url' => true
            )),
            'time_stamp' => $aBlog['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'blog',
            'like_type_id' => 'blog',
            'custom_data_cache' => $aBlog,
            'load_block' => 'blog.feed'
        ), $aRow);

        $aReturn['type_id'] = 'blog';

        // Strips all image in content
        list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aBlog['text'], 'img');
        $aReturn['feed_content'] = $sDescription;

        // Get image for feed
        if (!empty($aBlog['image_path'])) {
            $sImageSrc = Phpfox::getService('blog')->getImageUrl($aBlog['image_path'], $aBlog['server_id'], '_1024');
            $aReturn['feed_image'] = "<span style='background-image: url({$sImageSrc})'></span>";
        } else {
            $sImageSrc = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            if (!empty($sImageSrc)) {
                $aReturn['feed_image'] = "<span style='background-image: url({$sImageSrc})'></span>";
            }
        }

        $aCategories = Phpfox::getService('blog.category')->getCategoriesByBlogId($aBlog['blog_id']);
        $sHtmlCategories = '';

        if (!empty($aCategories)) {
            $sHtmlCategories = "<a href='" . $aCategories[0]['link'] . "'>" . $aCategories[0]['category_name'] . "</a>";
            unset($aCategories[0]);

            if (!empty($aCategories)) {
                $iCountCategories = count($aCategories);
                $sHtmlCategories .= sprintf(" %s <span class='dropup dropdown-tooltip' data-component='dropdown-tooltip'><a role='button' data-toggle=\"dropdown\" >%s %s</a>", _p('and'), $iCountCategories, $iCountCategories > 1 ? _p('others') : _p('other'));
                $sHtmlCategories .= '<ul class="dropdown-menu dropdown-center">';
                foreach ($aCategories as $aCategory) {
                    $sHtmlCategories .= sprintf("<li><a href='%s'>%s</a></li>", $aCategory['link'], $aCategory['category_name']);
                }
                $sHtmlCategories .= '</ul></span>';
            }
        }

        Phpfox_Component::setPublicParam('custom_param_blog_' . $aRow['feed_id'], ['aItem' => $aBlog,
            'sImageSrc' => $sImageSrc,
            'sLink' => Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']),
            'sCategory' => $sHtmlCategories
        ]);

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aBlog['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aBlog['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = db()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aBlog['group_id'])
                ->execute('getSlaveRow');

            if (empty($aPage)) {
                return false;
            }

            $aReturn['parent_user_name'] = Phpfox::getService($aBlog['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aBlog['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        return $aReturn;
    }

    /**
     * @param int $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool|null
     */
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = db()->select('blog_id, title, user_id')
            ->from(Phpfox::getT('blog'))
            ->where('blog_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }
        Phpfox::getService('blog.cache.remove')->blog($aRow['blog_id']);
        db()->updateCount('like', 'type_id = \'blog\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'blog',
            'blog_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('blog', $aRow['blog_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'blog.full_name_liked_your_blog_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                ))
                ->message(array(
                    'blog.full_name_liked_your_blog_link_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])
                ))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('blog_like', $aRow['blog_id'], $aRow['user_id']);
        }
        return null;
    }

    /**
     * @param array $aNotification
     *
     * @return mixed
     */
    public function getNotificationLike($aNotification)
    {
        $aRow = db()->select('b.blog_id, b.title, b.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('blog'), 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('users_liked_gender_own_blog_title', array(
                'users' => $sUsers,
                'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => $sTitle
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_blog_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param int $iItemId
     */
    public function deleteLike($iItemId)
    {
        Phpfox::getService('blog.cache.remove')->blog($iItemId);
        db()->updateCount('like', 'type_id = \'blog\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'blog',
            'blog_id = ' . (int)$iItemId);
    }

    /**
     * @param array $aRow
     * @param null $iUserId
     *
     * @return array
     */
    public function getNewsFeed($aRow, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);

        $oUrl = Phpfox::getLib('url');

        $aRow['text'] = _p('owner_full_name_added_a_new_blog_a_href_title_link_title_a',
            array(
                'owner_full_name' => $aRow['owner_full_name'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link']
            )
        );

        $aRow['icon'] = 'module/blog.png';
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'blog';

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);

        return $aRow;
    }

    /**
     * @param array $aRow
     * @param null $iUserId
     *
     * @return array
     */
    public function getCommentNewsFeed($aRow, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');

        if ($aRow['owner_user_id'] == $aRow['item_user_id']) {
            $aRow['text'] = _p('user_added_a_new_comment_on_their_own_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id')) {
            $aRow['text'] = _p('user_added_a_new_comment_on_your_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('user_name_added_a_new_comment_on_item_user_name_blog', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'item_user_name' => $aRow['viewer_full_name'],
                    'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
                )
            );
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    /**
     * @param array $aUser
     *
     * @return string
     */
    public function getTagLinkProfile($aUser)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettaglinkprofile__start')) ? eval($sPlugin) : false);
        return $this->getTagLink();
    }

    /**
     * @return string
     */
    public function getTagLink()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_gettaglink__start')) ? eval($sPlugin) : false);
        $sExtra = '';
        if (defined('PHPFOX_TAG_PARENT_MODULE')) {
            $sExtra .= PHPFOX_TAG_PARENT_MODULE . '.' . PHPFOX_TAG_PARENT_ID . '.';
        }

        return Phpfox::getLib('url')->makeUrl($sExtra . 'blog.tag');
    }

    /**
     * @param int $iId
     * @param null|int $iUserId
     */
    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);

        if ($iUserId === null) {
            $iUserId = Phpfox::getUserBy('user_id');
        }

        db()->insert(Phpfox::getT('track'), [
            'type_id' => 'blog',
            'item_id' => (int) $iId,
            'ip_address' => Phpfox::getIp(),
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        ]);
    }

    /**
     * @param int $iId
     * @param int $iUserId
     *
     * @return bool|array
     */
    public function getLatestTrackUsers($iId, $iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getlatesttrackusers__start')) ? eval($sPlugin) : false);

        $aRows = db()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('track'), 'track')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
            ->where('track.item_id = ' . (int)$iId . ' AND track.user_id != ' . (int)$iUserId . ' AND track.type_id="blog"')
            ->order('track.time_stamp DESC')
            ->limit(0, 6)
            ->execute('getSlaveRows');

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getlatesttrackusers__end')) ? eval($sPlugin) : false);
        return (count($aRows) ? $aRows : false);
    }

    /**
     * @return string
     */
    public function getTagTypeProfile()
    {
        return 'blog';
    }

    /**
     * @return string
     */
    public function getTagType()
    {
        return 'blog';
    }

    /**
     * @param int $iId
     * @param int $iChild
     *
     * @return bool|string
     */
    public function getFeedRedirect($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);

        $aBlog = db()->select('b.blog_id, b.title')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']);
    }

    /**
     * @return string user group setting
     */
    public function getAjaxCommentVar()
    {
        return 'blog.can_post_comment_on_blog';
    }

    /**
     * @param array $aVals
     * @param null|int $iUserId
     * @param null|string $sUserName
     */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aBlog = db()->select('u.full_name, u.user_id, u.gender, u.user_name, b.title, b.blog_id, b.privacy, b.privacy_comment')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment',
            $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
        Phpfox::getService('blog.cache.remove')->blog($aVals['item_id']);
        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            db()->updateCounter('blog', 'total_comment', 'blog_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aBlog['user_id'],
                'item_id' => $aBlog['blog_id'],
                'owner_subject' => _p('full_name_commented_on_your_blog_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aBlog['title'])),
                'owner_message' => _p('full_name_commented_on_your_blog_message',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aBlog['title'])),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'comment_blog',
                'mass_id' => 'blog',
                'mass_subject' => (Phpfox::getUserId() == $aBlog['user_id'] ? _p('full_name_commented_on_gender_blog',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aBlog['gender'], 1)
                    )) : _p('full_name_commented_on_blog_full_name_s_blog',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'blog_full_name' => $aBlog['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aBlog['user_id'] ? _p('full_name_commented_on_gender_blog_message',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aBlog['gender'], 1),
                        'link' => $sLink,
                        'title' => $aBlog['title']
                    )) : _p('full_name_commented_on_blog_full_name_s_blog_message', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'blog_full_name' => $aBlog['full_name'],
                    'link' => $sLink,
                    'title' => $aBlog['title']
                )))
            )
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    /**
     * @param array $aVals
     * @param string $sText
     */
    public function updateCommentText($aVals, $sText)
    {
    }

    /**
     * @param int $iId
     * @param string $sName
     *
     * @return string
     */
    public function getItemName($iId, $sName)
    {
        return _p('a_href_link_on_name_s_blog_a',
            array('link' => Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
    }

    /**
     * @return array
     */
    public function getAttachmentField()
    {
        return [
            'blog',
            'blog_id'
        ];
    }

    /**
     * @return string this value to generate a link
     */
    public function getProfileLink()
    {
        return 'profile.blog';
    }

    /**
     * @param int $iId
     *
     * @return array
     */
    public function getCommentItem($iId)
    {
        $aRow = db()->select('blog_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    /**
     * @param int $iId
     *
     * @return string
     */
    public function getRssTitle($iId)
    {
        $aRow = db()->select('title')
            ->from($this->_sTable)
            ->where('blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        return 'Comments on: ' . $aRow['title'];
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @return string
     */
    public function getCommentItemName()
    {
        return 'blog';
    }

    /**
     * @param string $sAction
     * @param int $iId
     */
    public function processCommentModeration($sAction, $iId)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_processcommentmoderation__start')) ? eval($sPlugin) : false);
        // Is this comment approved?
        if ($sAction == 'approve') {
            // Update the blog count
            Phpfox::getService('blog.process')->updateCounter($iId);

            // Get the blogs details so we can add it to our news feed
            $aBlog = db()->select('b.blog_id, b.user_id, b.title, b.title_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')
                ->from($this->_sTable, 'b')
                ->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'blog\' AND c.item_id = b.blog_id')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->where('b.blog_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            // Add to news feed
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_blog', $aBlog['blog_id'],
                $aBlog['text_parsed'], $aBlog['comment_user_id'], $aBlog['user_id'], $aBlog['comment_id']) : null);

            // Send the user an email
            if (Phpfox::getParam('core.is_personal_site')) {
                $sLink = Phpfox::getLib('url')->makeUrl('blog', $aBlog['title_url']);
            } else {
                $sLink = Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'), [
                    'blog',
                    $aBlog['title_url']
                ]);
            }

            Phpfox::getLib('mail')->to($aBlog['comment_user_id'])
                ->subject([
                    'comment.full_name_approved_your_comment_on_site_title',
                    [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'site_title' => Phpfox::getParam('core.site_title')
                    ]
                ])->message([
                    'comment.full_name_approved_your_comment_on_site_title_message',
                    [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'site_title' => Phpfox::getParam('core.site_title'),
                        'link' => $sLink
                    ]
                ])->notification('comment.approve_new_comment')->send();
        }
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_processcommentmoderation__end')) ? eval($sPlugin) : false);
    }

    /**
     * @return array
     */
    public function getWhatsNew()
    {
        return [
            'blog.blogs_title' => [
                'ajax' => '#blog.getNew?id=js_new_item_holder',
                'id' => 'blog',
                'block' => 'blog.new'
            ]
        ];
    }

    /**
     * @param string $sQuery
     * @param bool $bIsTagSearch
     *
     * @return array|null
     */
    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = 'b.is_approved = 1 AND b.privacy = 1 AND b.post_status = ' . BLOG_STATUS_PUBLIC;
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (b.title LIKE \'%' . db()->escape($sQuery) . '%\' OR bt.text_parsed LIKE \'%' . db()->escape($sQuery) . '%\')';
        }

        if ($bIsTagSearch == true) {
            db()->innerJoin(Phpfox::getT('tag'), 'tag',
                'tag.item_id = b.blog_id AND tag.category_id = \'blog\' AND tag.tag_url = \'' . db()->escape($sQuery) . '\'');
        }

        $iCnt = db()->select('COUNT(*)')
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('blog_text'), 'bt', 'bt.blog_id = b.blog_id')
            ->where($sCondition)
            ->execute('getSlaveField');

        if ($bIsTagSearch == true) {
            db()->innerJoin(Phpfox::getT('tag'), 'tag',
                'tag.item_id = b.blog_id AND tag.category_id = \'blog\' AND tag.tag_url = \'' . db()->escape($sQuery) . '\'')->group('b.blog_id');
        }

        $aRows = db()->select('b.title, b.title_url, b.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'b')
            ->join(Phpfox::getT('blog_text'), 'bt', 'bt.blog_id = b.blog_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('b.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('search_blogs');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div><input type="button" value="' . _p('view_more_blogs') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('blog',
                        array('tag', $sQuery)) . '\';" /></div>';
            } else {
                $aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('blog') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('view_more_blogs') . '" class="search_button" /></div></form>';
            }

            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('blog', $aRow['title_url'])),
                    'image' => Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aRow['server_id'],
                            'title' => $aRow['full_name'],
                            'path' => 'core.url_user',
                            'file' => $aRow['user_image'],
                            'suffix' => '_120',
                            'max_width' => 75,
                            'max_height' => 75
                        )
                    ),
                    'extra_info' => _p('blog_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox::getLib('url')->makeUrl('blog'),
                            'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'),
                                $aRow['time_stamp']),
                            'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);
            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * @param int $iId
     */
    public function deleteComment($iId)
    {
        db()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)), 'blog_id = ' . (int)$iId);
        Phpfox::getService('blog.cache.remove')->blog($iId);
    }

    /**
     * @param int $iItemId
     *
     * @return bool
     */
    public function verifyFavorite($iItemId)
    {
        $aItem = db()->select('i.blog_id')->from($this->_sTable, 'i')
            ->where('i.blog_id = ' . (int)$iItemId . ' AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.post_status = ' . BLOG_STATUS_PUBLIC)
            ->execute('getSlaveRow');

        if (!isset($aItem['blog_id'])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $aFavorites
     *
     * @return array
     */
    public function getFavorite($aFavorites)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfavorite__start')) ? eval($sPlugin) : false);
        $aItems = db()->select('i.title, i.time_stamp, i.title_url, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
            ->where('i.blog_id IN(' . implode(',',
                    $aFavorites) . ') AND i.is_approved = 1 AND i.privacy IN(1,2) AND i.post_status = ' . BLOG_STATUS_PUBLIC)
            ->execute('getSlaveRows');

        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display([
                'server_id' => $aItem['server_id'],
                'path' => 'core.url_user',
                'file' => $aItem['user_image'],
                'suffix' => '_75',
                'max_width' => 75,
                'max_height' => 75
            ]);

            if (Phpfox::getParam('core.is_personal_site')) {
                $aItems[$iKey]['link'] = Phpfox::getLib('url')->makeUrl('blog', $aItem['title_url']);
            } else {
                $aItems[$iKey]['link'] = Phpfox::getService('user')
                    ->getLink($aItem['user_id'], $aItem['user_name'], ['blog', $aItem['title_url']]);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfavorite__return')) ? eval($sPlugin) : false);
        return [
            'title' => _p('search_blogs'),
            'items' => $aItems
        ];
    }

    /**
     * @return array
     */
    public function getDashboardLinks()
    {
        return [
            'submit' => [
                'phrase' => _p('write_a_blog'),
                'link' => 'blog.add',
                'image' => 'misc/page_white_add.png'
            ],
            'edit' => [
                'phrase' => _p('manage_blogs'),
                'link' => 'profile.blog',
                'image' => 'misc/page_white_edit.png'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return [
            _p('blogs') => $aUser['activity_blog']
        ];
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     * @throws \Exception
     */
    public function onDeleteUser($iUser)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
        // get all the blogs by this user
        $aBlogs = db()->select('blog_id')->from($this->_sTable)->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aBlogs as $aBlog) {
            Phpfox::getService('blog.process')->delete($aBlog['blog_id'], true);
        }
        // delete this user's categories
        $aCats = db()->select('category_id')->from(Phpfox::getT('blog_category'))
            ->where('user_id = ' . (int)$iUser)->execute('getSlaveRows');
        $sCats = '1=2';
        foreach ($aCats as $aCat) {
            $sCats .= ' OR category_id = ' . $aCat['category_id'];
        }
        db()->delete(Phpfox::getT('blog_category'), $sCats);
        db()->delete(Phpfox::getT('blog_category_data'), $sCats);

        // delete the tracks
        db()->delete(Phpfox::getT('track'), 'user_id = ' . $iUser . ' AND type_id="blog"');
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
    }

    /**
     * This callback will be called when a page or group be deleted
     * @param $iId
     * @param $sType
     * @throws \Exception
     */
    public function onDeletePage($iId, $sType)
    {
        $aBlogs = db()->select('blog_id')->from(':blog')->where([
            'module_id' => $sType,
            'item_id' => $iId
        ])->executeRows();
        foreach ($aBlogs as $aBlog) {
            Phpfox::getService('blog.process')->delete($aBlog['blog_id'], true);
        }
    }

    /**
     * @return bool|null
     */
    public function getItemView()
    {
        if (Phpfox::getLib('request')->get('req3') != '') {
            return true;
        }
        return null;
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedApproved($aRow)
    {
        return [
            'message' => _p('your_blog_blog_title_has_been_approved', [
                'blog_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')
            ]),
            'link' => Phpfox::getLib('url')->makeUrl('blog', ['redirect' => $aRow['item_id']])
        ];
    }

    /**
     * @return array
     */
    public function spamCheck()
    {
        return [
            'phrase' => _p('blogs'),
            'value' => Phpfox::getService('blog')->getSpamTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('blog', ['view' => 'spam'])
        ];
    }

    /**
     * @param array $aRequest
     *
     * @return array|bool|string
     */
    public function legacyRedirect($aRequest)
    {
        if (isset($aRequest['req2'])) {
            switch ($aRequest['req2']) {
                case 'view':
                    if (isset($aRequest['id'])) {
                        $aItem = Phpfox::getService('core')->getLegacyUrl([
                            'url_field' => 'title_url',
                            'table' => 'blog',
                            'field' => 'upgrade_blog_id',
                            'id' => $aRequest['id']
                        ]);

                        if ($aItem !== false) {
                            return [$aItem['user_name'], ['blog', $aItem['title_url']]];
                        }
                    }
                    break;
                default:
                    return 'blog';
                    break;
            }
        }

        return false;
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getCommentNotification($aNotification)
    {
        $aRow = db()->select('b.blog_id, b.title, b.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('blog'), 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = _p('users_commented_on_gender_blog_title', array(
                'users' => $sUsers,
                'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => $sTitle
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_your_blog_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getCommentNotificationFeed($aRow)
    {
        return [
            'message' => _p('full_name_wrote_a_comment_on_your_blog_blog_title', [
                'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                'full_name' => $aRow['full_name'],
                'blog_link' => Phpfox::getLib('url')->makeUrl('blog', ['redirect' => $aRow['item_id']]),
                'blog_title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')
            ]),
            'link' => Phpfox::getLib('url')->makeUrl('blog', ['redirect' => $aRow['item_id']]),
            'path' => 'core.url_user',
            'suffix' => '_50'
        ];
    }

    /**
     * @return array
     */
    public function reparserList()
    {
        return [
            'name' => _p('blogs_text'),
            'table' => 'blog_text',
            'original' => 'text',
            'parsed' => 'text_parsed',
            'item_field' => 'blog_id'
        ];
    }

    /**
     * @return array
     */
    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return [
            'phrase' => _p('blogs'),
            'value' => db()->select('COUNT(*)')->from(Phpfox::getT('blog'))
                ->where('post_status = ' . BLOG_STATUS_PUBLIC . ' AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }

    /**
     * @return bool|null
     */
    public function checkFeedShareLink()
    {
        if (!Phpfox::getUserParam('blog.add_new_blog')) {
            return false;
        }
        return null;
    }

    /**
     * @param int $iId
     * @param int $iChildId
     *
     * @return bool|string
     */
    public function getFeedRedirectFeedLike($iId, $iChildId = 0)
    {
        return $this->getFeedRedirect($iChildId);
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNewsFeedFeedLike($aRow)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id']) {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_a_href_link_blog_a', [
                'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
                'link' => $aRow['link']
            ]);
        } else {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_blog_a',
                [
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
                    'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
                    'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
                    'link' => $aRow['link']
                ]);
        }

        $aRow['icon'] = 'misc/thumb_up.png';
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_ondeleteuser__end')) ? eval($sPlugin) : false);
        return $aRow;
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedNotifyLike($aRow)
    {
        return array(
            'message' => _p('a_href_user_link_full_name_a_likes_your_a_href_link_blog_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                    'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
                    'link' => Phpfox::getLib('url')->makeUrl('blog', array('redirect' => $aRow['item_id']))
                )
            ),
            'link' => Phpfox::getLib('url')->makeUrl('blog', array('redirect' => $aRow['item_id']))
        );
    }

    /**
     * @return array
     */
    public function updateCounterList()
    {
        $aList = [];

        $aList[] = [
            'name' => _p('users_blog_count'),
            'id' => 'blog-total'
        ];

        $aList[] = [
            'name' => _p('update_tags_blogs'),
            'id' => 'blog-tag-update'
        ];

//        $aList[] = [
//            'name' => _p('update_users_activity_blog_points'),
//            'id'   => 'blog-activity'
//        ];

        return $aList;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return int
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);

        if ($iId == 'blog-total') {
            $iCnt = db()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = db()->select('u.user_id, u.user_name, u.full_name, COUNT(b.blog_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('blog'), 'b',
                    'b.user_id = u.user_id AND b.is_approved = 1 AND b.post_status = ' . BLOG_STATUS_PUBLIC)
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                db()->update(Phpfox::getT('user_field'), array('total_blog' => $aRow['total_items']),
                    'user_id = ' . $aRow['user_id']);
            }
        } elseif ($iId == 'blog-activity') {
            $iCnt = db()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            db()->select('u.user_id, u.user_group_id, COUNT(oc.blog_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('blog'), 'oc', 'oc.user_id = u.user_id')
                ->group('u.user_id')
                ->union();

            $aRows = db()->select('m.user_id, u.user_group_id, m.activity_blog, m.activity_points, m.activity_total, u.total_items')
                ->unionFrom('u')
                ->join(Phpfox::getT('user_activity'), 'm', 'u.user_id = m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');


            foreach ($aRows as $aRow) {
                $iPointsPerBlog = Phpfox::getService('user.group.setting')->getGroupParam($aRow['user_group_id'],
                    'blog.points_blog');

                $aUpdate = array(
                    'activity_points' => (($aRow['activity_total'] - ($aRow['activity_blog'] * $iPointsPerBlog)) + ($aRow['total_items'] * $iPointsPerBlog)),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_blog']) + $aRow['total_items']),
                    'activity_blog' => $aRow['total_items']
                );

                db()->update(Phpfox::getT('user_activity'), $aUpdate, 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(Phpfox::getT('tag'))
            ->where('category_id = \'blog\'')
            ->execute('getSlaveField');

        $aRows = db()->select('m.tag_id, oc.blog_id AS tag_item_id')
            ->from(Phpfox::getT('tag'), 'm')
            ->where('m.category_id = \'page_id\'')
            ->leftJoin(Phpfox::getT('blog'), 'oc', 'oc.blog_id = m.item_id')
            ->limit($iPage, $iPageLimit, $iCnt)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            if (empty($aRow['tag_item_id'])) {
                db()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
            }
        }

        return $iCnt;
    }

    /**
     * @return array
     */
    public function getActivityPointField()
    {
        return [
            _p('blogs') => 'activity_blog'
        ];
    }

    /**
     * @return array
     */
    public function pendingApproval()
    {
        return [
            'phrase' => _p('blogs'),
            'value' => Phpfox::getService('blog')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('blog', ['view' => 'pending'])
        ];
    }

    /**
     * @return array
     */
    public function getSqlTitleField()
    {
        return [
            [
                'table' => 'blog',
                'field' => 'title',
                'has_index' => 'title'
            ],
            [
                'table' => 'blog_category',
                'field' => 'name'
            ]
        ];
    }

    /**
     * @return string
     */
    public function getAjaxProfileController()
    {
        return 'blog.index';
    }

    /**
     * @param array $aUser
     *
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getUserParam('blog.view_blogs')) {
            return false;
        }

        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_blog'])) {
                return false;
            }

            if (isset($aUser['total_blog']) && (int)$aUser['total_blog'] === 0) {
                return false;
            }
        }

        $aSubMenu = [];

        if ($aUser['user_id'] == Phpfox::getUserId() && $this->request()->get('req2') == 'blog') {
            $aSubMenu[] = [
                'phrase' => _p('drafts'),
                'url' => Phpfox::getLib('url')->makeUrl('profile.blog.view_draft'),
                'total' => Phpfox::getService('blog')->getTotalDrafts($aUser['user_id'])
            ];
        }

        $aMenus[] = [
            'phrase' => _p('blogs'),
            'url' => 'profile.blog',
            'total' => Phpfox::getService('blog')->getProfileTotal($aUser['user_id']),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/blog.png'
        ];

        return $aMenus;
    }

    /**
     * @param int $iUserId
     *
     * @return array
     */
    public function getTotalItemCount($iUserId)
    {
        return [
            'field' => 'total_blog',
            'total' => db()->select('COUNT(*)')->from(Phpfox::getT('blog'))
                ->where('user_id = ' . (int)$iUserId . ' AND is_approved = 1 AND post_status = ' . BLOG_STATUS_PUBLIC)
                ->execute('getSlaveField')
        ];
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationApproved($aNotification)
    {
        $aRow = db()->select('b.blog_id, b.title, b.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('blog'), 'b')->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (!isset($aRow['blog_id'])) {
            return false;
        }

        $sPhrase = _p('your_blog_title_has_been_approved', [
            'title' => Phpfox::getLib('parse.output')
                ->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')
        ]);

        return [
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
            'no_profile_image' => true
        ];
    }

    /**
     * @param string $sSearch
     */
    public function globalUnionSearch($sSearch)
    {
        $sConds = Phpfox::getService('blog')->getConditionsForSettingPageGroup('item');
        db()->select('item.blog_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'blog\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('blog'), 'item')
            ->where(db()->searchKeywords('item.title',
                    $sSearch) . ' AND item.is_approved = 1 AND item.privacy = ' . PRIVACY_EVERYONE . ' AND item.post_status = ' . BLOG_STATUS_PUBLIC . $sConds)
            ->union();
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('blog', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('blog');
        if (!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'blog.url_photo',
                    'suffix' => '_500',
                    'max_width' => '320',
                    'max_height' => '320'
                )
            );
        }
        return $aInfo;
    }

    /**
     * @return array
     */
    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('blog')
        );
    }

    /**
     * @return array
     */
    public function getGlobalPrivacySettings()
    {
        return [
            'blog.default_privacy_setting' => [
                'phrase' => _p('blogs')
            ]
        ];
    }

    /**
     * @param array $aNotification
     *
     * @return array
     */
    public function getCommentNotificationTag($aNotification)
    {
        $aRow = db()->select('b.blog_id, b.title, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('blog'), 'b', 'b.blog_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_blog', ['user_name' => $aRow['full_name']]);

        return [
            'link' => Phpfox::getLib('url')->permalink('blog', $aRow['blog_id'],
                    $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [];

        $aPerms['blog.share_blogs'] = _p('who_can_share_blogs');
        $aPerms['blog.view_browse_blogs'] = _p('who_can_view_blogs');

        return $aPerms;
    }

    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = array();
        $aPerms['blog.share_blogs'] = _p('Who can share blogs?');
        return $aPerms;
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'],
                'blog.view_browse_blogs') || !Phpfox::getUserParam('blog.view_blogs')) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('blogs'),
            'url' => Phpfox::getService('pages')
                    ->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'blog/',
            'icon' => 'module/blog.png',
            'landing' => 'blog'
        ];

        return $aMenus;
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getGroupMenu($aPage)
    {
        if (!Core\Lib::appsGroup()->hasPerm($aPage['page_id'],
                'blog.view_browse_blogs') || !Phpfox::getUserParam('blog.view_blogs')) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('Blogs'),
            'url' => Core\Lib::appsGroup()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'blog/',
            'icon' => 'module/blog.png',
            'landing' => 'blog'
        ];

        return $aMenus;
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'],
                'blog.share_blogs') || !Phpfox::getUserParam('blog.add_new_blog')) {
            return null;
        }

        return [
            [
                'phrase' => _p('add_new_blog'),
                'url' => Phpfox::getLib('url')->makeUrl('blog.add', [
                    'module' => 'pages',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getGroupSubMenu($aPage)
    {
        if (!Core\Lib::appsGroup()->hasPerm($aPage['page_id'],
                'blog.share_blogs') || !Phpfox::getUserParam('blog.add_new_blog')) {
            return null;
        }

        return [
            [
                'phrase' => _p('add_new_blog'),
                'url' => Phpfox::getLib('url')->makeUrl('blog.add', [
                    'module' => 'groups',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @param int $iPage
     *
     * @return bool
     */
    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage,
                'blog.view_browse_blogs') || !Phpfox::getUserParam('blog.view_blogs')) {
            return false;
        }

        return true;
    }

    /**
     * @param int $iItemId
     * @param bool $bReturnTittle
     *
     * @return array|string
     */
    public function getItemLink($iItemId, $bReturnTittle = false)
    {
        $aBlog = Phpfox::getService('blog')->getBlog($iItemId);
        $sUrl = Phpfox::getLib('url')->permalink('blog', $iItemId, $aBlog['title']);
        if ($bReturnTittle) {
            return [
                'title' => $aBlog['title'],
                'url' => $sUrl
            ];
        } else {
            return $sUrl;
        }
    }

    /**
     * @param int $iId
     * @return array in the format:
     * array(
     *    'title' => 'item title',            <-- required
     *    'link'  => 'makeUrl()'ed link',            <-- required
     *    'paypal_msg' => 'message for paypal'        <-- required
     *    'item_id' => int                <-- required
     *    'user_id;   => owner's user id            <-- required
     *    'error' => 'phrase if item doesnt exit'        <-- optional
     *    'extra' => 'description'            <-- optional
     *    'image' => 'path to an image',            <-- optional
     *    'image_dir' => 'photo.url_photo|...        <-- optional (required if image)
     *    'server_id' => db value                <-- optional (required if image)
     * )
     */
    public function getToSponsorInfo($iId)
    {
        $aBlog = db()->select('b.user_id, b.blog_id as item_id, b.title, b.image_path as image, b.server_id')
            ->from($this->_sTable, 'b')
            ->where('b.blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aBlog)) {
            return array('error' => _p('sponsor_error_not_found_blog'));
        }

        $aBlog['title'] = _p('sponsor_title_blog', array('sBlogTitle' => $aBlog['title']));
        $aBlog['paypal_msg'] = _p('sponsor_paypal_message_blog', array('sBlogTitle' => $aBlog['title']));
        $aBlog['link'] = Phpfox::permalink('blog', $aBlog['item_id'], $aBlog['title']);
        $aBlog['image_dir'] = 'blog.url_photo';

        $aBlog = array_merge($aBlog, [
            'redirect_completed' => 'blog',
            'message_completed' => _p('purchase_blog_sponsor_completed'),
            'redirect_pending_approval' => 'blog',
            'message_redirect_pending_approval' => _p('purchase_blog_sponsor_pending_approval')
        ]);

        return $aBlog;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('blog.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return false;
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationNewItem_Groups($aNotification)
    {
        if (!Phpfox::isModule('groups')) {
            return false;
        }
        $aBlog = Phpfox::getService('blog')->getBlog($aNotification['item_id']);
        if (empty($aBlog) || empty($aBlog['item_id']) || ($aBlog['module_id'] != 'groups')) {
            return false;
        }

        $aRow = Core\Lib::appsGroup()->getPage($aBlog['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('{{ users }} add a new blog in the group "{{ title }}"', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox::getLib('url')->permalink('blog', $aBlog['blog_id'], $aBlog['title']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @description: callback to check permission to view a blog
     * @param $iId
     *
     * @return array|bool
     */
    public function canViewItem($iId)
    {
        return Phpfox::getService('blog')->canViewItem($iId);
    }

    public function ignoreDeleteLikesAndTagsWithFeed()
    {
        return true;
    }

    public function enableSponsor($aParams)
    {
        return Phpfox::getService('blog.process')->sponsor((int)$aParams['item_id'], 1);
    }

    /**
     * @param array $aParams
     *
     * @return bool|string
     */
    public function getLink($aParams)
    {
        $aBlog = $this->database()
            ->select('b.blog_id, b.title')
            ->from(Phpfox::getT('blog'), 'b')
            ->where('b.blog_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');
        if (empty($aBlog)) {
            return false;
        }
        return Phpfox::permalink('blog', $aBlog['blog_id'], $aBlog['title']);
    }

    /**
     * @return array
     */
    public function getUploadParams() {
        return Phpfox::getService('blog')->getUploadPhotoParams();
    }

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':blog')
            ->where('user_id =' . (int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('blogs'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    public function processInstallRss()
    {
        (new \Apps\Core_Blogs\Installation\Version\v453())->importToRssFeed();
    }
}
