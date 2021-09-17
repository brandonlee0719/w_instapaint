<?php
namespace Apps\Core_Blogs\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use SebastianBergmann\CodeCoverage\Report\PHP;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Process
 * @package Apps\Core_Blogs\Service
 */
class Process extends Phpfox_Service
{
    const MAX_LENGTH_INPUT = 255;

    /**
     * @var string
     */
    protected $_sTable = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog');
    }

    /**
     * Add new blog item
     *
     * @param array $aVals
     *
     * @return int
     */
    public function add($aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.service_process__start')) ? eval($sPlugin) : false);

        $oFilter = Phpfox::getLib('parse.input');

        if (!empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            if (Phpfox::isModule($aVals['module_id'])) {
                $aVals['privacy'] = PRIVACY_EVERYONE;
                $aVals['privacy_comment'] = PRIVACY_EVERYONE;
            } else {
                Phpfox_Error::set(_p('Cannot find the parent item.'));
                return false;
            }
        }

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['text'] . ' ' . $aVals['title']);

        if (!Phpfox::getParam('blog.allow_links_in_blog_title')) {
            if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
                return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
            }
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = PRIVACY_EVERYONE;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = PRIVACY_EVERYONE;
        }

        $sTitle = $oFilter->clean($aVals['title'], self::MAX_LENGTH_INPUT);
        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog'));

        if (!isset($aVals['post_status'])) {
            $aVals['post_status'] = BLOG_STATUS_PUBLIC;
        }

        $iPostStatus = (int)$aVals['post_status'];

        $aInsert = [
            'user_id' => Phpfox::getUserId(),
            'title' => $sTitle,
            'time_stamp' => PHPFOX_TIME,
            'is_approved' => ACTIVATE,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : PRIVACY_EVERYONE),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : PRIVACY_EVERYONE),
            'post_status' => $iPostStatus,
            'total_attachment' => 0
        ];

        if (isset($aVals['item_id']) && isset($aVals['module_id'])) {
            $aInsert['item_id'] = (int)$aVals['item_id'];
            $aInsert['module_id'] = $oFilter->clean($aVals['module_id']);
        }

        $bIsSpam = false;
        if (Phpfox::getParam('blog.spam_check_blogs')) {
            if (Phpfox::getLib('spam')->check([
                'action' => 'isSpam',
                'params' => [
                    'module' => 'blog',
                    'content' => $oFilter->prepare($aVals['text'])
                ]
            ])
            ) {
                $aInsert['is_approved'] = BLOG_BANNED;
                $bIsSpam = true;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($aInsert['user_id'], $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                    return false;
                }
                $aInsert['image_path'] = $aFile['path'];
                $aInsert['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update($aInsert['user_id'], 'blog', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }

        if (Phpfox::getUserParam('blog.approve_blogs') && $iPostStatus != BLOG_STATUS_DRAFT) {
            $aInsert['is_approved'] = '0';
            $bIsSpam = true;
            //Remove total pending blog
            $this->cache()->remove();
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_add_start')) ? eval($sPlugin) : false);

        $iId = db()->insert(Phpfox::getT('blog'), $aInsert);

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_add_end')) ? eval($sPlugin) : false);

        db()->insert(Phpfox::getT('blog_text'), array(
                'blog_id' => $iId,
                'text' => $oFilter->clean($aVals['text']),
                'text_parsed' => $oFilter->prepare($aVals['text'])
            )
        );

        // Process categories for blog
        if (!empty($aVals['selected_categories'])) {
            Phpfox::getService('blog.category.process')
                ->addCategoryForBlog($iId, $aVals['selected_categories'],
                    ($aVals['post_status'] == BLOG_STATUS_PUBLIC ? true : false));
        }

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('blog', $iId, Phpfox::getUserId(), $aVals['text'], true);
        }
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
            Phpfox::getService('tag.process')->add('blog', $iId, Phpfox::getUserId(), $aVals['tag_list']);
        }

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        if ($bIsSpam === true) {
            return $iId;
        }

        if ($aVals['post_status'] == BLOG_STATUS_PUBLIC) {
            if (isset($aVals['module_id']) && ($aVals['module_id'] != 'blog') && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'],
                    'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVals['module_id'] . '.getFeedDetails',
                    $aVals['item_id']))->add('blog', $iId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $aVals['item_id']) : null);
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('blog', $iId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aVals['module_id']) && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'],
                    'addItemNotification')) {
                Phpfox::callback($aVals['module_id'] . '.addItemNotification', [
                    'page_id' => $aVals['item_id'],
                    'item_perm' => 'blog.view_browse_blogs',
                    'item_type' => 'blog',
                    'item_id' => $iId,
                    'owner_id' => Phpfox::getUserId(),
                    'items_phrase' => _p('blogs__l')
                ]);
            }

            // Update user activity
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'blog', '+');
        }

        if ($aVals['privacy'] == PRIVACY_CUSTOM) {
            Phpfox::getService('privacy.process')->add('blog', $iId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_process__end')) ? eval($sPlugin) : false);

        $this->cache()->remove('blog_topic');
        Phpfox::getService('blog.cache.remove')->my();
        return $iId;
    }

    /**
     * Update an exist blog
     *
     * @param int $iId
     * @param int $iUserId
     * @param array $aVals
     * @param null|array $aRow is OldBlogData
     *
     * @return int
     */
    public function update($iId, $iUserId, $aVals, &$aRow = null)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.service_process_update__start')) ? eval($sPlugin) : false);

        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text']);

        if (!Phpfox::getParam('blog.allow_links_in_blog_title')) {
            if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
                return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
            }
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = PRIVACY_EVERYONE;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = PRIVACY_EVERYONE;
        }

        $sTitle = $oFilter->clean($aVals['title'], self::MAX_LENGTH_INPUT);
        $bHasAttachments = !empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog');

        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $iPostStatus = (isset($aVals['post_status']) ? $aVals['post_status'] : BLOG_STATUS_PUBLIC);

        //Publish a draft blog, but this user group's blog have to approve first.
        if ($aRow['post_status'] == BLOG_STATUS_DRAFT && $iPostStatus != BLOG_STATUS_DRAFT && Phpfox::getUserParam('blog.approve_blogs')) {
            $this->cache()->remove();
            $aRow['is_approved'] = 0;
        }

        $aUpdate = array(
            'title' => $sTitle,
            'time_update' => PHPFOX_TIME,
            'is_approved' => $aRow['is_approved'],
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : PRIVACY_EVERYONE),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : PRIVACY_EVERYONE),
            'post_status' => $iPostStatus,
            'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iId,
                'blog', true) : '0')
        );

        // Mean that draft publish
        if ((int)$aRow['post_status'] == BLOG_STATUS_DRAFT && (int)$aVals['post_status'] == BLOG_STATUS_PUBLIC) {
            $aUpdate['time_stamp'] = PHPFOX_TIME;
        }

        if (Phpfox::getParam('blog.spam_check_blogs')) {
            if (Phpfox::getLib('spam')->check([
                'action' => 'isSpam',
                'params' => [
                    'module' => 'blog',
                    'content' => $oFilter->prepare($aVals['text'])
                ]
            ])
            ) {
                $aUpdate['is_approved'] = BLOG_BANNED;
            }
        }

        if (!empty($aRow['image_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
            if ($this->deleteImage($aRow['image_path'], $aRow['user_id'], $aRow['server_id'])) {
                $aUpdate['image_path'] = null;
                $aUpdate['server_id'] = 0;
            }
            else {
                return false;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($aRow['user_id'], $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                    return false;
                }
                $aUpdate['image_path'] = $aFile['path'];
                $aUpdate['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update($aRow['user_id'], 'blog', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_update')) ? eval($sPlugin) : false);

        db()->update(Phpfox::getT('blog'), $aUpdate, 'blog_id = ' . (int)$iId);
        db()->update(Phpfox::getT('blog_text'), array(
            'text' => $oFilter->clean($aVals['text']),
            'text_parsed' => $oFilter->prepare($aVals['text'])
        ), 'blog_id = ' . (int)$iId);

        // Update categories for the editing blog
        Phpfox::getService('blog.category.process')->updateCategoryForBlog($iId, $aVals['selected_categories'],
            ($aVals['post_status'] == BLOG_STATUS_PUBLIC ? true : false),
            ((isset($aVals['draft_publish']) && $aVals['draft_publish']) ? false : true));

        // Add hastag
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('blog', $iId, $iUserId, $aVals['text'], true);
        }
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support')) {
            Phpfox::getService('tag.process')->update('blog', $iId, $iUserId,
                (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
        }

        if ($aRow['is_approved'] == 1 && (int)$aRow['post_status'] == BLOG_STATUS_DRAFT && (int)$aVals['post_status'] == BLOG_STATUS_PUBLIC) {
            if (isset($aRow['module_id']) && ($aRow['module_id'] != 'blog') && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aRow['module_id'] . '.getFeedDetails',
                    $aRow['item_id']))->add('blog', $iId, $aVals['privacy'], $aVals['privacy_comment'],
                    $aRow['item_id'], $iUserId) : null);
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('blog', $iId, $aVals['privacy'],
                    $aVals['privacy_comment'], 0, $iUserId) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aRow['module_id']) && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'addItemNotification')) {
                Phpfox::callback($aRow['module_id'] . '.addItemNotification', [
                    'page_id' => $aRow['item_id'],
                    'item_perm' => 'blog.view_browse_blogs',
                    'item_type' => 'blog',
                    'item_id' => $iId,
                    'owner_id' => $iUserId,
                    'items_phrase' => _p('blogs__l')
                ]);
            }

            // Update user activity
            Phpfox::getService('user.activity')->update($iUserId, 'blog');
        } else {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('blog', $iId, $aVals['privacy'],
                $aVals['privacy_comment']) : null);
        }

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == PRIVACY_CUSTOM) {
                Phpfox::getService('privacy.process')->update('blog', $iId,
                    (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []));
            } else {
                Phpfox::getService('privacy.process')->delete('blog', $iId);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_update__end')) ? eval($sPlugin) : false);
        $this->cache()->remove("blog_detail_view_" . (int)$iId);
        $this->cache()->remove("blog_detail_edit_" . (int)$iId);
        $this->cache()->remove("blog_topic");

        Phpfox::getService('blog.cache.remove')->my();
        return $iId;
    }

    /**
     * Process delete blog item
     *
     * @param int $iId
     * @param bool $bForce
     * @return boolean
     * @throws \Exception
     */
    public function delete($iId, $bForce = false)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.service_process_delete__start')) ? eval($sPlugin) : false);
        $aBlog = Phpfox::getService('blog')->getBlogForEdit($iId);
        $bCanDelete = Phpfox::getService('blog.permission')->canDelete($iId);
        if (!$bForce && !$bCanDelete) {
            return Phpfox_Error::set('unable_to_delete_this_item_due_to_privacy_settings');
        }

        //delete image
        if (!empty($aBlog['image_path'])) {
            $this->deleteImage($aBlog['image_path'], $aBlog['user_id'], $aBlog['server_id']);
        }

        db()->delete(Phpfox::getT('blog'), "blog_id = " . (int)$iId);
        db()->delete(Phpfox::getT('blog_text'), "blog_id = " . (int)$iId);
        db()->delete(Phpfox::getT('track'), 'item_id = ' . (int)$iId . ' AND type_id="blog"');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('blog', (int)$iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_blog', $iId) : null);

        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('blog', (int)$iId, 0, true) : null);
        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
            'blog_like',
            'blog_approved'
        ], (int)$iId) : null);

        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('blog', (int)$iId) : null);

        // Update user activity
        Phpfox::getService('user.activity')->update($aBlog['user_id'], 'blog', '-');

        $aRows = db()->select('blog_id, category_id')
            ->from(Phpfox::getT('blog_category_data'))
            ->where('blog_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        if (count($aRows)) {
            foreach ($aRows as $aRow) {
                db()->delete(Phpfox::getT('blog_category_data'),
                    "blog_id = " . (int)$aRow['blog_id'] . " AND category_id = " . (int)$aRow['category_id']);
                db()->updateCount('blog_category_data', 'category_id = ' . (int)$aRow['category_id'], 'used',
                    'blog_category', 'category_id = ' . (int)$aRow['category_id']);
            }
        }

        // Delete hash-tag
        if (Phpfox::isModule('tag')) {
            (Phpfox::isModule('tag') ? db()->delete(Phpfox::getT('tag'),
                'item_id = ' . $aBlog['blog_id'] . ' AND category_id = \'blog\'') : null);
            $this->cache()->remove();
        }

        // Delete attachment
        (Phpfox::isModule('attachment') ? Phpfox::getService('attachment.process')->deleteForItem($aBlog['user_id'], $aBlog['blog_id'],
            'blog') : null);
        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aBlog['user_id'], $aBlog['blog_id'],
            'blog') : null);
        (Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($aBlog['user_id'], $aBlog['blog_id'], 'blog') : null);

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_delete')) ? eval($sPlugin) : false);

        $this->cache()->remove('blog_topic');
        Phpfox::getService('blog.cache.remove')->my();
        Phpfox::getService('blog.cache.remove')->blog($aBlog['blog_id']);

        return true;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function updateView($iId)
    {
        db()->update($this->_sTable, ['total_view' => 'total_view + 1'], ['blog_id' => (int)$iId], false);

        return true;
    }

    /**
     * @param int $iId
     * @param bool $bMinus
     */
    public function updateCounter($iId, $bMinus = false)
    {
        db()->update($this->_sTable, ['total_comment' => 'total_comment ' . ($bMinus ? "-" : "+") . ' 1'],
            ['blog_id' => (int)$iId], false);
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function approve($iId)
    {
        Phpfox::getUserParam('blog.can_approve_blogs', true);

        $aBlog = db()->select('b.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('blog'), 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return Phpfox_Error::set(_p('the_blog_you_are_trying_to_approve_is_not_valid'));
        }

        if ($aBlog['is_approved'] == '1') {
            return false;
        }

        db()->update(Phpfox::getT('blog'), array('is_approved' => '1', 'time_stamp' => PHPFOX_TIME),
            'blog_id = ' . $aBlog['blog_id']);

        if ($aBlog['post_status'] == BLOG_STATUS_PUBLIC) {
            if (isset($aBlog['module_id']) && ($aBlog['module_id'] != 'blog') && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'],
                    'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aBlog['module_id'] . '.getFeedDetails',
                    $aBlog['item_id']))->add('blog', $iId, $aBlog['privacy'], $aBlog['privacy_comment'],
                    $aBlog['item_id'], $aBlog['user_id']) : null);
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('blog', $iId, $aBlog['privacy'],
                    $aBlog['privacy_comment'], 0, $aBlog['user_id']) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aBlog['module_id']) && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'],
                    'addItemNotification')) {
                Phpfox::callback($aBlog['module_id'] . '.addItemNotification', [
                    'page_id' => $aBlog['item_id'],
                    'item_perm' => 'blog.view_browse_blogs',
                    'item_type' => 'blog',
                    'item_id' => $iId,
                    'owner_id' => $aBlog['user_id'],
                    'items_phrase' => _p('blogs__l')
                ]);
            }
        }

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('blog_approved', $aBlog['blog_id'], $aBlog['user_id']);
        }

        if ($aBlog['is_approved'] == BLOG_BANNED) {
            db()->updateCounter('user', 'total_spam', 'user_id', $aBlog['user_id'], true);
        }

        Phpfox::getService('user.activity')->update($aBlog['user_id'], 'blog');

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_approve__1')) ? eval($sPlugin) : false);

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('blog', $aBlog['blog_id'], $aBlog['title']);
        Phpfox::getLib('mail')->to($aBlog['user_id'])
            ->subject(array(
                'blog.your_blog_has_been_approved_on_site_title',
                array('site_title' => Phpfox::getParam('core.site_title'))
            ))
            ->message(array(
                'blog.your_blog_has_been_approved_on_site_title_message',
                array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)
            ))
            ->notification('blog.blog_is_approved')
            ->send();
        //clear cache
        $this->cache()->remove();
        return true;
    }

    /**
     * Feature a blog item.
     *
     * @param $iId
     * @param $sType | 0 mean un-feature and 1 mean feature
     * @return bool|resource
     */
    public function feature($iId, $sType)
    {
        if (is_array($iId)) {
            $bSuccess = db()->update($this->_sTable,
                array('is_featured' => ((int)$sType == ACTIVATE ? ACTIVATE : INACTIVATE)),
                'blog_id IN (' . implode(',', $iId) . ') AND is_approved = 1 AND post_status = ' . BLOG_STATUS_PUBLIC);
        } else {
            $bSuccess = db()->update($this->_sTable,
                array('is_featured' => ((int)$sType == ACTIVATE ? ACTIVATE : INACTIVATE)), 'blog_id = ' . (int)$iId);
        }

        return $bSuccess;
    }

    /**
     * Sponsor an item. This item will be showed up in block Sponsor Blog in Blog Home Page
     *
     * @param $iId
     * @param $sType
     * @return bool
     */
    public function sponsor($iId, $sType)
    {
        if (!Phpfox::getUserParam('blog.can_sponsor_blog') && !Phpfox::getUserParam('blog.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$sType;
        if (!in_array($iType, [ACTIVATE, INACTIVATE])) {
            return false;
        }

        db()->update($this->_sTable, array('is_sponsor' => $iType), 'blog_id = ' . (int)$iId);

        (($sPlugin = Phpfox_Plugin::get('blog.service_process_sponsor__end')) ? eval($sPlugin) : false);
        return true;
    }

    public function deleteImage($sName = '', $iUserId, $iServerId = 0) {
        if (empty($sName)) {
            return false;
        }

        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $aParams = Phpfox::getService('blog')->getUploadPhotoParams();
        $aParams['type'] = 'blog';
        $aParams['path'] = $sName;
        $aParams['user_id'] = $iUserId;
        $aParams['update_space'] = ($iUserId ? true : false);
        $aParams['server_id'] = $iServerId;

        return Phpfox::getService('user.file')->remove($aParams);
    }

    /**
     * @param string $sMethod
     * @param array $aArguments
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('blog.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return false;
    }
}
