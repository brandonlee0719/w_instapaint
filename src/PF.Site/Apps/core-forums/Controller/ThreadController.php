<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Locale;
use Phpfox_Module;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ThreadController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('forum.can_view_forum', true);

        $iPage = $this->request()->getInt('page');
        $iPageSize = Phpfox::getParam('forum.total_posts_per_thread');
        $aThreadCondition = array();
        $aCallback = $this->getParam('aCallback', null);

        if (($iPostRedirect = $this->request()->getInt('permalink')) && ($sUrl = Phpfox::getService('forum.callback')->getFeedRedirectPost($iPostRedirect))) {
            $this->url()->forward(preg_replace('/\/post_(.*)\//i', '/view_\\1/', $sUrl));
        }


        if (($iRedirect = $this->request()->getInt('redirect')) && ($aThread = Phpfox::getService('forum.thread')->getForRedirect($iRedirect))) {
            if ((Phpfox::isModule('pages') || Phpfox::isModule('groups')) && $aThread['group_id'] > 0 && ($sParentId = Phpfox::getPagesType($aThread['group_id'])) && Phpfox::isModule($sParentId)) {
                $aCallback = Phpfox::callback($sParentId . '.addForum', $aThread['group_id']);
                if (isset($aCallback['module'])) {
                    $aCallback['module_id'] = $aCallback['module'];
                    $this->url()->send($aCallback['url_home'], array('forum', $aThread['title_url']));
                }
            }
            $this->url()->send('forum',
                array($aThread['forum_url'] . '-' . $aThread['forum_id'], $aThread['title_url']));
        }

        $threadId = $this->request()->getInt('req3');
        if ($this->request()->segment(3) == 'replies' && $this->request()->getInt('id')) {
            $threadId = $this->request()->getInt('id');
            $iPage = 1;
            $iPageSize = 200;
            $this->template()->setBreadCrumb(_p('latest_replies'), $this->url()->current(), true);
            $this->template()->assign([
                'isReplies' => true
            ]);
        }

        $aThreadCondition[] = 'ft.thread_id = ' . $threadId . '';

        $sPermaView = $this->request()->get('view', null);
        if ((int)$sPermaView <= 0) {
            $sPermaView = null;
        }

        list($iCnt, $aThread) = Phpfox::getService('forum.thread')->getThread($aThreadCondition, array(),
            'fp.time_stamp ASC', $iPage, $iPageSize, $sPermaView);

        if (!isset($aThread['thread_id'])) {
            return Phpfox_Error::display(_p('not_a_valid_thread'));
        }

        if ($aThread['group_id'] > 0 &&  (Phpfox::isModule('pages') || Phpfox::isModule('groups')) && ($sParentId = Phpfox::getPagesType($aThread['group_id'])) && Phpfox::isModule($sParentId)) {
            $aCallback = Phpfox::callback($sParentId . '.addForum', $aThread['group_id']);
            if (isset($aCallback['module']) && !isset($aCallback['module_id'])) {
                $aCallback['module_id'] = $aCallback['module'];
            }
            if (!Phpfox::getService($sParentId)->hasPerm($aThread['group_id'], 'forum.view_browse_forum')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        if ($aThread['view_id'] != '0' && $aThread['user_id'] != Phpfox::getUserId()) {
            if (!Phpfox::getUserParam('forum.can_approve_forum_thread') && !Phpfox::getService('forum.moderate')->hasAccess($aThread['forum_id'],
                    'approve_thread')
            ) {
                return Phpfox_Error::display(_p('not_a_valid_thread'));
            }
        }

        if ($aCallback === null && !Phpfox::getService('forum')->hasAccess($aThread['forum_id'], 'can_view_forum')) {
            if (Phpfox::isUser()) {
                return Phpfox_Error::display(_p('you_do_not_have_the_proper_permission_to_view_this_thread'));
            } else {
                return Phpfox_Error::display(_p('log_in_to_view_thread'));
            }

        }

        if ($aCallback === null && !Phpfox::getService('forum')->hasAccess($aThread['forum_id'],
                'can_view_thread_content')
        ) {
            $this->url()->send('forum', null, _p('you_do_not_have_the_proper_permission_to_view_this_thread'));
        }

        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

        $aForum = Phpfox::getService('forum')
            ->id($aThread['forum_id'])
            ->getForum();

        if ($this->request()->get('approve') && (Phpfox::getUserParam('forum.can_approve_forum_thread') || Phpfox::getService('forum.moderate')->hasAccess($aThread['forum_id'],
                    'approve_thread')) && $aThread['view_id']
        ) {
            $sCurrentUrl = $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title']);

            if (Phpfox::getService('forum.thread.process')->approve($aThread['thread_id'])) {
                $this->url()->forward($sCurrentUrl);
            }
        }

        if ($iPostId = $this->request()->getInt('post')) {
            $iCurrentPage = Phpfox::getService('forum.post')->getPostPage($aThread['thread_id'], $iPostId, $iPageSize);

            $sFinalLink = $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title'], false, null,
                array('page' => $iCurrentPage));

            $this->url()->forward($sFinalLink . '#post' . $iPostId);
        }
        // Increment the view counter
        $bUpdateCounter = false;

        if (Phpfox::isModule('track')) {
            if (!Phpfox::getUserBy('is_invisible')) {
                if (!$aThread['is_seen']) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('forum', $aThread['thread_id']);
                } else {
                    if (!setting('track.unique_viewers_counter')) {
                        $bUpdateCounter = true;
                        Phpfox::getService('track.process')->add('forum', $aThread['thread_id']);
                    } else {
                        Phpfox::getService('track.process')->update('forum_thread', $aThread['thread_id']);
                    }
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            Phpfox::getService('forum.thread.process')->updateTrack($aThread['thread_id'], true);
        }


        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('forum',
                $aThread['thread_id']);
            if (isset($aTags[$aThread['thread_id']])) {
                $aThread['tag_list'] = $aTags[$aThread['thread_id']];
            }
        }

        // Add tags to meta keywords
        if (!empty($aThread['tag_list']) && $aThread['tag_list'] && Phpfox::isModule('tag')) {
            $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aThread['tag_list']));
        }
        $aThread['bookmark'] = $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title']);

        $this->setParam('iActiveForumId', $aForum['forum_id']);

        if (Phpfox::getParam('forum.rss_feed_on_each_forum')) {
            if ($aCallback === null) {
                $this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('forum') . ': ' . Phpfox::getSoftPhrase($aForum['name']) . '" href="' . $this->url()->makeUrl('forum',
                        array('rss', 'forum' => $aForum['forum_id'])) . '" />');
            } else {
                $this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('group_forum') . ': ' . $aCallback['title'] . '" href="' . $this->url()->makeUrl('forum',
                        array('rss', 'group' => $aCallback['group_id'])) . '" />');
            }
        }

        if (Phpfox::getParam('forum.enable_rss_on_threads')) {
            $this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('thread') . ': ' . $aThread['title'] . '" href="' . $this->url()->makeUrl('forum',
                    array('rss', 'thread' => $aThread['thread_id'])) . '" />');
        }

        if ($aCallback === null) {
            $this->template()->setBreadCrumb(_p('forum'), $this->url()->makeUrl('forum'))
                ->setBreadCrumb($aForum['breadcrumb'])->setBreadCrumb(Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])),
                    $this->url()->permalink('forum', $aForum['forum_id'], $aForum['name']));
        } else {
            $this->template()->setBreadCrumb((isset($aCallback['module_title']) ? $aCallback['module_title'] : _p('pages')),
                $this->url()->makeUrl($aCallback['module']));
            $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            $this->template()->setBreadCrumb(_p('discussions'), $aCallback['url_home'] . 'forum/');
        }

        Phpfox::getService('forum.thread')->getThreadPermission($aThread, $aCallback);

        $sCurrentThreadLink = ($aCallback === null ? $this->url()->makeUrl('forum', array(
            $aForum['name_url'] . '-' . $aForum['forum_id'],
            $aThread['title_url']
        )) : $this->url()->makeUrl($aCallback['url_home'], $aThread['title_url']));

        if ($this->request()->get('view')) {
            Phpfox_Module::instance()->appendPageClass('single_mode');
        }
        if ($aThread['canReply'] && !$aThread['forum_is_closed'] && !$aThread['is_closed']) {
            $this->template()
                ->menu(_p('reply'), '#',
                    'onclick="$Core.box(\'forum.reply\', 800, \'id=' . $aThread['thread_id'] . '\'); return false;"');

        }

        $aJsLoad = array(
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'switch_legend.js' => 'static_script',
            'switch_menu.js' => 'static_script',
            'share.js' => 'module_attachment'
        );
        if (!empty($aThread['poll'])) {
            $aJsLoad = array_merge($aJsLoad, ['poll.js' => 'module_poll', 'poll.css' => 'module_poll',]);
        }

        if ($aThread['view_id']) {
            $aTitleLabel = [
                'type_id' => 'forum-thread',
                'label' => [
                    'pending' => [
                        'title' => '',
                        'title_class' => 'flag-style-arrow',
                        'icon_class' => 'clock-o'
                    ]
                ]
            ];
            $aPendingItem = [
                'message' => _p('thread_is_pending_approval'),
                'actions' => []
            ];
            if ($aThread['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'forum.approveThread\', \'inline=true&amp;id='.$aThread['thread_id'].'\')'
                ];
            }
            if ($aThread['canEdit']) {
                if ($aCallback === null) {
                    $sEditLink = $this->url()->makeUrl('forum.post.thread',['edit' => $aThread['thread_id']]);
                } else {
                    $sEditLink = $this->url()->makeUrl('forum.post.thread',['edit' => $aThread['thread_id'],'module' => $aCallback['module_id'],'item' => $aCallback['item_id']]);
                }
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $sEditLink
                ];
            }
            if ($aThread['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_ajax' => true,
                    'label' => _p('delete'),
                    'action' => '$Core.forum.deleteThread(\''.$aThread['thread_id'].'\')',
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem,
                'aTitleLabel' => $aTitleLabel
            ]);
        }
        $this->template()->setTitle($aThread['title'])
            ->setBreadCrumb($aThread['title'],
                $this->url()->permalink('forum.thread', $aThread['thread_id'], $aThread['title']), true)
            ->setMeta('description', $aThread['title'] . ' - ' . Phpfox::getSoftPhrase($aForum['name']))
            ->setMeta('keywords', $this->template()->getKeywords($aThread['title']))
            ->setPhrase(array(
                    'provide_a_reply',
                    'adding_your_reply',
                    'are_you_sure',
                    'post_successfully_deleted',
                    'are_you_sure_you_want_to_delete_this_thread_permanently'
                )
            )
            ->setEditor()
            ->setHeader('cache', $aJsLoad
            )
            ->assign(array(
                    'aThread' => $aThread,
                    'aPost' => (isset($aThread['post_starter']) ? $aThread['post_starter'] : ''),
                    'iTotalPosts' => $iCnt,
                    'sCurrentThreadLink' => $sCurrentThreadLink,
                    'aCallback' => $aCallback,
                    'sPermaView' => $sPermaView,
                    'aPoll' => (empty($aThread['poll']) ? false : $aThread['poll']),
                    'bIsViewingPoll' => true,
                    'bIsCustomPoll' => true,
                    'sMicroPropType' => 'CreativeWork',
                    'sShareDescription' => !empty($aThread['post_starter']) ? str_replace(array("\n", "\r", "\r\n"), '',
                        $aThread['post_starter']['text']) : '',
                )
            );

        if (!empty($aThread['post_starter'])) {
            $this->template()->setMeta('description', ' - ' . $aThread['post_starter']['text']);
        }
        $aModerationMenu = [];
        $bShowModerator = false;
        if (!empty($aThread['has_pending_post']) && Phpfox::getUserParam('forum.can_approve_forum_post')) {
            $aModerationMenu[] = [
                'phrase' => _p('approve'),
                'action' => 'approve',
            ];
        }
        if (Phpfox::getUserParam('forum.can_delete_other_posts')) {
            $aModerationMenu[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
            $bShowModerator = true;
        }

        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'forumpost',
                    'ajax' => 'forum.postModeration',
                    'menu' => $aModerationMenu
                )
            );
        }
        $this->template()->assign(['bShowModerator' => ($bShowModerator)]);
        Phpfox::getLib('parse.output')->setEmbedParser(array(
                'width' => 640,
                'height' => 360
            )
        );

        if ($this->request()->get('is_ajax_get')) {
            $this->template()->assign('isReplies', true);
            Phpfox_Module::instance()->getControllerTemplate();
            $content = ob_get_contents();
            ob_clean();

            return [
                'run' => "$('.thread_replies .tr_view_all').remove();",
                'html' => [
                    'to' => '.tr_content',
                    'with' => $content
                ]
            ];
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_thread_clean')) ? eval($sPlugin) : false);
    }
}