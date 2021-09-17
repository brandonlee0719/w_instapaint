<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ViewController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($playId = $this->request()->get('play'))) {
            \Phpfox::getService('music.process')->play($this->request()->get('play'));

            return [
                'played' => true
            ];
        }

        Phpfox::getUserParam('music.can_access_music', true);


        if (!($aSong = \Phpfox::getService('music')->getSong($this->request()->get('req2'))) || ($aSong['view_id'] && !Phpfox::getUserParam('music.can_approve_songs') && $aSong['user_id'] != Phpfox::getUserId())) {
            return \Phpfox_Error::display(_p('the_song_you_are_looking_for_cannot_be_found'));
        }

        if (Phpfox::isUser() && \Phpfox::getService('user.block')->isBlocked(null, $aSong['user_id'])) {
            return \Phpfox_Module::instance()->setController('error.invalid');
        }


        $aCallback = false;
        if (!empty($aSong['module_id'])) {
            if ($aCallback = Phpfox::callback($aSong['module_id'] . '.getMusicDetails', $aSong)) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
                if (isset($aSong['module_id']) && Phpfox::isModule($aSong['module_id']) && Phpfox::hasCallback($aSong['module_id'],
                        'checkPermission')
                ) {
                    if (!Phpfox::callback($aSong['module_id'] . '.checkPermission', $aSong['item_id'],
                        'music.view_browse_music')
                    ) {
                        return \Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                    }
                }
            }
        }

        if (Phpfox::isModule('privacy')) {
            \Phpfox::getService('privacy')->check('music_song', $aSong['song_id'], $aSong['user_id'], $aSong['privacy'],
                $aSong['is_friend']);
        }


        $this->setParam('aSong', $aSong);
        $this->setParam('aRatingCallback', array(
                'type' => 'music_song',
                'total_rating' => _p('total_rating_ratings', array('total_rating' => $aSong['total_rating'])),
                'default_rating' => $aSong['total_score'],
                'item_id' => $aSong['song_id'],
                'stars' => array(
                    '2' => _p('poor'),
                    '4' => _p('nothing_special'),
                    '6' => _p('worth_listening_too'),
                    '8' => _p('pretty_cool'),
                    '10' => _p('awesome')
                )
            )
        );
        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aSong['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('music', 'song_' . $aSong['song_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('music', 'song_' . $aSong['song_id']);
                } else {
                    Phpfox::getService('track.process')->update('music_song', $aSong['song_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            db()->updateCounter('music_song', 'total_view', 'song_id', $aSong['song_id']);
        }
        $this->template()->setBreadCrumb(_p('music'),
            ($aCallback === false ? $this->url()->makeUrl('music') : $aCallback['url_home_photo']))
            ->setMeta('keywords', Phpfox::getParam('music.music_meta_keywords'))
            ->setMeta('keywords', $aSong['title'])
            ->setMeta('description', Phpfox::getParam('music.music_meta_description'))
            ->setMeta('description', $aSong['description']);
        if (!empty($aSong['album_url'])) {
            $this->template()->setBreadCrumb($aSong['album_url'],
                $this->url()->permalink('music.album', $aSong['album_id'], $aSong['album_url']));
        }
        $this->template()->setBreadCrumb($aSong['title'],
            $this->url()->permalink('music', $aSong['song_id'], $aSong['title']), true);

        $this->setParam('aFeed', array(
                'comment_type_id' => 'music_song',
                'privacy' => $aSong['privacy'],
                'comment_privacy' => Phpfox::getUserParam('music.can_add_comment_on_music_song') ? 0 : 3,
                'like_type_id' => 'music_song',
                'feed_is_liked' => $aSong['is_liked'],
                'feed_is_friend' => $aSong['is_friend'],
                'item_id' => $aSong['song_id'],
                'user_id' => $aSong['user_id'],
                'total_comment' => $aSong['song_total_comment'],
                'total_like' => $aSong['total_like'],
                'feed_link' => $this->url()->permalink('music', $aSong['song_id'], $aSong['title']),
                'feed_title' => $aSong['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aSong['total_like'],
                'report_module' => 'music_song',
                'report_phrase' => _p('report_this_song_lowercase')
            )
        );
        if (Phpfox::isModule('rate')) {
            $this->template()
                ->setHeader('cache', array(
                    'rate.js' => 'module_rate'
                ))
                ->setHeader(array(
                    '<script type="text/javascript">$Behavior.rateSong = function() {  $Core.rate.init({module: \'music_song\', display: ' . ($aSong['has_rated'] ? 'false' : ($aSong['user_id'] == Phpfox::getUserId() ? 'false' : 'true')) . ', error_message: \'' . ($aSong['has_rated'] ? _p('you_have_already_voted',
                        array('phpfox_squote' => true)) : _p('you_cannot_rate_your_own_song',
                        array('phpfox_squote' => true))) . '\'}); }</script>'
                ));
        }
        Phpfox::getService('music')->getPermissions($aSong);
        if (!empty($aSong['image_path'])) {
            $this->template()->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aSong['server_id'],
                    'path' => 'music.url_image',
                    'file' => $aSong['image_path'],
                    'suffix' => '',
                    'return_url' => true
                )
            ));
        } else {
            $this->template()->setMeta('og:image', Phpfox::getParam('music.default_song_photo'));
        }
        $aTitleLabel = [
            'type_id' => 'music'
       ];

        if ($aSong['is_featured']) {
            $aTitleLabel['label']['featured'] =[
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class'  => 'diamond'

            ];
        }
        if ($aSong['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class'  => 'sponsor'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;
        if ($aSong['view_id'] != 0) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('song_is_pending_approval'),
                'actions' => []
            ];
            if ($aSong['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'music.approveSong\', \'inline=true&amp;id='.$aSong['song_id'].'\')'
                ];
            }
            if ($aSong['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('music.upload',['id' => $aSong['song_id']]),
                ];
            }
            if ($aSong['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_song'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('music.delete',['id' => $aSong['song_id']]),
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }

        $this->template()->setTitle($aSong['title'])
            ->setMeta(array(
                'og:image:type' => 'image/jpeg',
                'og:image:width' => '500',
                'og:image:height' => '500'
            ))
            ->setHeader('cache', array(
                    'jquery/plugin/star/jquery.rating.js' => 'static_script',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'jscript/mediaelementplayer/mediaelement-and-player.js' => 'app_core-music'
                )
            )
            ->setEditor(array(
                    'load' => 'simple'
                )
            )
            ->assign(array(
                    'aSong' => $aSong,
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aSong['description']),
                    'aTitleLabel' => $aTitleLabel
                )
            );
        \Phpfox::getService('music')->getSectionMenu();

        if ($sPlugin = \Phpfox_Plugin::get('music.component_controller_music_view')) {
            eval($sPlugin);
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}