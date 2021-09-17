<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Core_Component_Block_Template_Notification extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aPageLastLogin = ((Phpfox::isModule('pages') && Phpfox::getUserBy('profile_page_id')) ? Phpfox::getService('pages')->getLastLogin() : false);
        $this->template()->assign(array(
                'iGlobalProfilePageId' => Phpfox::getUserBy('profile_page_id'),
                'aGlobalProfilePageLogin' => $aPageLastLogin,
                'sPageCoverDefaultUrl' => Phpfox::getParam('pages.default_cover_photo'),
            )
        );

        if (Phpfox::isUser()) {
            if (!$aPageLastLogin) {
                $aUser = Phpfox::getService('user')->get(Phpfox::getUserId());
                if (!empty($aUser['cover_photo'])) {
                    $aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aUser['cover_photo']);
                    $this->template()->assign('aCoverPhoto', $aCoverPhoto);
                }

                if (Phpfox::isModule('subscribe')) {
                    $subcribeModule = Phpfox_Module::instance()->get('subscribe');
                    if ($subcribeModule and $subcribeModule['is_active']) {
                        $this->template()->assign('showMembership', true);
                    }
                }

                $this->template()->assign([
                    'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
                    'aUser' => $aUser,
                ]);
            } elseif (Phpfox::isModule('pages')) {
                // login as page
                $aPage = Phpfox::getService('pages')->getPage(Phpfox::getUserBy('profile_page_id'));
                $aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aPage['cover_photo_id']);
                $this->template()->assign('aCoverPhoto', $aCoverPhoto);
            }
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('core.component_block_template_notification_clean')) ? eval($sPlugin) : false);
    }
}
