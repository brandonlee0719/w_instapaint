<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class RssController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this->request()->getInt('forum')) {
            if (!Phpfox::getParam('forum.rss_feed_on_each_forum')) {
                return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
            }

            if (!Phpfox::getService('forum')->hasAccess($this->request()->getInt('forum'), 'can_view_forum')) {
                return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
            }

            $aRss = Phpfox::getService('forum')->getForRss($this->request()->getInt('forum'));
        } elseif ($this->request()->getInt('thread')) {
            if (!Phpfox::getParam('forum.enable_rss_on_threads')) {
                return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
            }

            if (!Phpfox::getService('forum')->hasAccess($this->request()->getInt('thread'),
                'can_view_thread_content')
            ) {
                return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
            }

            $aRss = Phpfox::getService('forum.post')->getForRss($this->request()->getInt('thread'));

            if (isset($aRss['items']) && is_array($aRss['items']) && count($aRss['items'])) {
                if (!Phpfox::getService('forum')->hasAccess($aRss['items'][0]['forum_id'], 'can_view_forum')) {
                    return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
                }
            }
        } elseif ($this->request()->getInt('pages') || $this->request()->getInt('groups')) {
            if (!Phpfox::getParam('forum.rss_feed_on_each_forum')) {
                return Phpfox_Error::set(_p('rss_feeds_are_disabled_for_threads'));
            }

            $bIsGroup = $this->request()->getInt('groups');

            if (!$bIsGroup) {
                $aGroup = Phpfox::getService('pages')->getPage($this->request()->getInt('pages'));
            } else {
                $aGroup = Phpfox::getService('groups')->getPage($this->request()->getInt('groups'));
            }

            if (!isset($aGroup['page_id'])) {
                Phpfox_Error::reset();
                return Phpfox_Error::set($bIsGroup ? _p('not_a_valid_group') : _p('not_a_valid_page'));
            }

            $aItems = Phpfox::getService('forum.thread')->getForRss(Phpfox::getParam('rss.total_rss_display'), null,
                $aGroup['page_id']);

            $aRss = array(
                'href' => '',
                'title' => ($bIsGroup ? _p('latest_threads_in_group_forum') : _p('latest_threads_in_page_forum')) . ': ' . $aGroup['title'],
                'description' => _p('latest_threads_on') . ': ' . $aGroup['title'],
                'items' => $aItems
            );
        }

        isset($aRss) && Phpfox::getService('rss')->output($aRss);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_rss_clean')) ? eval($sPlugin) : false);
    }
}