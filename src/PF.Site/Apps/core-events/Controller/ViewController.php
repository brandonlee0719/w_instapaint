<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Events\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_EVENT_VIEW', true);

class ViewController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this->request()->get('req2') == 'view' && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('event_id', 'title'),
                    'table' => 'event',
                    'redirect' => 'event',
                    'title' => $sLegacyTitle
                )
            );
        }

        Phpfox::getUserParam('event.can_access_event', true);

        $sEvent = $this->request()->get('req2');

        if (!($aEvent = Phpfox::getService('event')->getEvent($sEvent))) {
            return Phpfox_Error::display(_p('the_event_you_are_looking_for_does_not_exist_or_has_been_removed'), 404);
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aEvent['user_id'])) {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('event', $aEvent['event_id'], $aEvent['user_id'], $aEvent['privacy'],
                $aEvent['is_friend']);
        }
        Phpfox::getService('event')->getPermissions($aEvent);
        $this->setParam([
            'aEvent' => $aEvent,
            'aEventDetail' => $aEvent,
            'allowTagFriends' => false
        ]);

        $bCanPostComment = (Phpfox::getUserParam('event.can_post_comment_on_event') || $aEvent['user_id'] == Phpfox::getUserId()) ? true : false;

        $aCallback = false;
        if ($aEvent['item_id'] && Phpfox::hasCallback($aEvent['module_id'], 'viewEvent')) {
            $aCallback = Phpfox::callback($aEvent['module_id'] . '.viewEvent', $aEvent['item_id']);
            $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            if (isset($aEvent['module_id']) && Phpfox::isModule($aEvent['module_id']) && Phpfox::hasCallback($aEvent['module_id'],
                    'checkPermission')
            ) {
                if (!Phpfox::callback($aEvent['module_id'] . '.checkPermission', $aEvent['item_id'],
                    'event.view_browse_events')
                ) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }

        /**
         * Check if current user is admin of event's parent item
         */
        $bIsAdmin = false;
        if ($aEvent['module_id'] && Phpfox::hasCallback($aEvent['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aEvent['module_id'] . '.isAdmin', $aEvent['item_id']);
        }

        if (Phpfox::getUserId()) {
            $bIsBlocked = Phpfox::getService('user.block')->isBlocked($aEvent['user_id'], Phpfox::getUserId());
            if ($bIsBlocked) {
                $bCanPostComment = false;
            }
        }

        defined('PHPFOX_IS_EVENT_VIEW') || define('PHPFOX_IS_EVENT_VIEW', true);
        $this->setParam('aFeedCallback', array(
                'module' => 'event',
                'table_prefix' => 'event_',
                'ajax_request' => 'event.addFeedComment',
                'item_id' => $aEvent['event_id'],
                'disable_share' => ($bCanPostComment ? false : true),
                'disable_sort' => true
            )
        );

        $this->setParam('aFeed', array(
            'feed_display' => 'mini',
            'privacy' => $aEvent['privacy'],
            'comment_privacy' => Phpfox::getUserParam('event.can_post_comment_on_event') ? 0 : 3,
            'like_type_id' => 'event',
            'feed_is_liked' => (isset($aEvent['is_liked']) ? $aEvent['is_liked'] : false),
            'feed_is_friend' => (isset($aEvent['is_friend']) ? $aEvent['is_friend'] : false),
            'item_id' => $aEvent['event_id'],
            'user_id' => $aEvent['user_id'],
            'total_comment' => $aEvent['total_comment'],
            'feed_total_like' => $aEvent['total_like'],
            'total_like' => $aEvent['total_like'],
            'feed_link' => Phpfox::getLib('url')->permalink('event', $aEvent['event_id'], $aEvent['title']),
            'feed_title' => $aEvent['title'],
            'type_id' => 'event',
            'report_module' => 'event',
            'report_phrase' => _p('event.report_an_event')
        ));

        if ($aEvent['view_id'] == '1') {
            $this->template()->setHeader('<script type="text/javascript">$Behavior.eventIsPending = function(){ $(\'#js_block_border_feed_display\').addClass(\'js_moderation_on\').hide(); }</script>');
        }

        if (Phpfox::getUserId() == $aEvent['user_id']) {
            define('PHPFOX_FEED_CAN_DELETE', true);
        }
        // Increment the view counter
        $bUpdateCounter = false;

        if (Phpfox::isModule('track')) {
            if (!Phpfox::getUserBy('is_invisible')) {
                if (!$aEvent['is_viewed']) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('event', $aEvent['event_id']);
                } else {
                    if (!setting('track.unique_viewers_counter')) {
                        $bUpdateCounter = true;
                        Phpfox::getService('track.process')->add('event', $aEvent['event_id']);
                    } else {
                        Phpfox::getService('track.process')->update('event', $aEvent['event_id']);
                    }
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            Phpfox::getService('event.process')->updateCounter($aEvent['event_id'], 'total_view');
        }
        if (!empty($aEvent['image_path'])) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aEvent['server_id'],
                'path' => 'event.url_image',
                'file' => $aEvent['image_path'],
                'suffix' => '',
                'return_url' => true
            ));
        } else {
            $sImage = Phpfox::getParam('event.event_default_photo');
        }
        $aMetaTags = [
            'og:image:type' => 'image/jpeg',
            'og:image:width' => '600',
            'og:image:height' => '600',
            'og:image' => $sImage
        ];
        $aTitleLabel = [
            'type_id' => 'event'
        ];

        if ($aEvent['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'diamond'

            ];
        }
        if ($aEvent['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'sponsor'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        if ($aEvent['view_id'] == 1) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('event_is_pending_approval'),
                'actions' => []
            ];
            if ($aEvent['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'event.approve\', \'inline=true&amp;event_id='.$aEvent['event_id'].'\', \'POST\')'
                ];
            }
            if ($aEvent['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('event.add',['id' => $aEvent['event_id']]),
                ];
            }
            if ($aEvent['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_event_permanently'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('event',['delete' => $aEvent['event_id']]),
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }

        $this->template()->setTitle($aEvent['title'])
            ->setMeta($aMetaTags)
            ->setMeta('keywords', Phpfox::getParam('event.event_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('event.event_meta_description'))
            ->setMeta('description', $aEvent['title'])
            ->setMeta('description', $aEvent['description'])
            ->setMeta('keywords', $this->template()->getKeywords($aEvent['title']))
            ->setBreadCrumb(_p('events'),
                ($aCallback === false ? $this->url()->makeUrl('event') : $this->url()->makeUrl($aCallback['url_home_pages'])))
            ->setBreadCrumb($aEvent['title'], $this->url()->permalink('event', $aEvent['event_id'], $aEvent['title']),
                true)
            ->setEditor(array(
                    'load' => 'simple'
                )
            )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'feed.js' => 'module_feed',
                )
            )
            ->assign(array(
                    'aEvent' => $aEvent,
                    'aCallback' => $aCallback,
                    'sMicroPropType' => 'Event',
                    'bIsAdmin' => $bIsAdmin,
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aEvent['description']),
                    'sAddThisPubId' => Phpfox::getParam('core.addthis_pub_id'),
                    'aTitleLabel' => $aTitleLabel,
                    'bIsDetail' => true
                )
            );

        Phpfox::getService('event')->buildSectionMenu();

        (($sPlugin = Phpfox_Plugin::get('event.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}
