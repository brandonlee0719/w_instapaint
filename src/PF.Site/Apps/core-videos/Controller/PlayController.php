<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class PlayController extends Phpfox_Component
{
    public function process()
    {
        user('pf_video_view', '1', null, true);
        $aCallback = $this->getParam('aCallback', false);
        $iVideo = $this->request()->get(($aCallback !== false ? $aCallback['request'] : 'req3'));

        if (!($aVideo = Phpfox::getService('v.video')->callback($aCallback)->getVideo($iVideo))) {
            Phpfox_Error::display(_p('the_video_you_are_looking_for_does_not_exist_or_has_been_removed'));
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aVideo['user_id'])) {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

        if (isset($aVideo['module_id']) && !empty($aVideo['item_id']) && !Phpfox::isModule($aVideo['module_id'])) {
            return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
        }

        if (isset($aVideo['module_id']) && $aVideo['module_id'] != 'video' && !empty($aVideo['item_id']) && Phpfox::isModule($aVideo['module_id'])) {
            if ($aVideo['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aVideo['item_id'],
                    'pf_video.view_browse_videos')) {
                return Phpfox_Error::display(_p('unable_to_view_this_section_due_to_privacy_settings'));
            } else {
                if (Phpfox::hasCallback($aVideo['module_id'],
                        'checkPermission') && !Phpfox::callback($aVideo['module_id'] . '.checkPermission',
                        $aVideo['item_id'], 'pf_video.view_browse_videos')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('v', $aVideo['video_id'], $aVideo['user_id'], $aVideo['privacy'],
                $aVideo['is_friend']);
        }

        if (isset($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'], 'getVideoDetails')) {
            if ($aCallback = Phpfox::callback($aVideo['module_id'] . '.getVideoDetails', $aVideo)) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            }
        }

        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aVideo['video_is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('v', $aVideo['video_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('v', $aVideo['video_id']);
                } else {
                    Phpfox::getService('track.process')->update('v', $aVideo['video_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            db()->updateCounter('video', 'total_view', 'video_id', $aVideo['video_id']);
        }

        $this->setParam('aVideo', $aVideo);
        $this->setParam('aFeed', array(
                'comment_type_id' => 'v',
                'privacy' => $aVideo['privacy'],
                'comment_privacy' => user('pf_video_comment', 1) ? 0 : 3,
                'like_type_id' => 'v',
                'feed_is_liked' => (isset($aVideo['is_liked']) ? $aVideo['is_liked'] : false),
                'feed_is_friend' => $aVideo['is_friend'],
                'item_id' => $aVideo['video_id'],
                'user_id' => $aVideo['user_id'],
                'total_comment' => $aVideo['total_comment'],
                'total_like' => $aVideo['total_like'],
                'feed_link' => Phpfox::permalink('video.play', $aVideo['video_id'], $aVideo['title']),
                'feed_title' => $aVideo['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aVideo['total_like'],
                'report_module' => 'v',
                'report_phrase' => _p('report_this_video')
            )
        );

        $aMetaTags = [
            'og:type' => 'video',
            'og:image' => $aVideo['image_path'],
            'og:image:width' => 640,
            'og:image:height' => 360
        ];
        $bLoadCheckin = false;
        if (Phpfox::isModule('feed') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key')) {
            $this->template()->setHeader('cache', array(
                    'places.js' => 'module_feed'
                )
            );
            $bLoadCheckin = true;
        }
        $aTitleLabel = [
            'type_id' => 'video'
        ];

        if ($aVideo['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'diamond'
            ];
        }
        if ($aVideo['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'sponsor'
            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;
        if ($aVideo['view_id'] == 2) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('video_is_pending_approval'),
                'actions' => []
            ];
            if ($aVideo['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'v.approve\', \'video_id='.$aVideo['video_id'].'&is_detail=1\')'
                ];
            }
            if ($aVideo['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('video.edit',['id' => $aVideo['video_id']]),
                ];
            }
            if ($aVideo['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_video_permanently'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('video',['delete' => $aVideo['video_id']])
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        define('PHPFOX_APP_DETAIL_PAGE', true);
        header("X-XSS-Protection: 0");
        
        $this->template()->setTitle($aVideo['title'])
            ->setBreadcrumb(_p('Videos'),
                ($aCallback === false ? $this->url()->makeUrl('video') : $aCallback['url_home_photo']))
            ->setBreadcrumb($aVideo['title'], $aVideo['link'], true)
            ->setMeta('description', $aVideo['text'])
            ->setMeta('keywords', $this->template()->getKeywords($aVideo['title']))
            ->setMeta($aMetaTags)
            ->keepBody(true)
            ->setEditor(array(
                    'load' => 'simple'
                )
            )
            ->assign(array(
                    'aItem' => $aVideo,
                    'sView' => 'play',
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aVideo['text']),
                    'bLoadCheckin' => $bLoadCheckin,
                    'aTitleLabel' => $aTitleLabel
                )
            );
        (($sPlugin = Phpfox_Plugin::get('video.component_controller_play_process')) ? eval($sPlugin) : false);

        return 'controller';
    }
}
