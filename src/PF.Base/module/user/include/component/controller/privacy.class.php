<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Privacy
 */
class User_Component_Controller_Privacy extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        if (!Phpfox::getUserParam('user.can_control_notification_privacy') && !Phpfox::getUserParam('user.can_control_profile_privacy')) {
            return Phpfox_Error::display(_p('privacy_settings_have_been_disabled_for_your_user_group'));
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if (Phpfox::getService('user.privacy.process')->update($aVals)) {
                $this->url()->send('user.privacy', ['tab' => empty($aVals['current_tab']) ? '' : $aVals['current_tab']], _p('privacy_settings_successfully_updated'));
            }
        }

        list($aUserPrivacy, $aNotifications, $aProfiles, $aItems) = Phpfox::getService('user.privacy')->get();

        $aUserInfo = Phpfox::getService('user')->get(Phpfox::getUserId());

        (($sPlugin = Phpfox_Plugin::get('user.component_controller_index_process')) ? eval($sPlugin) : false);

        $aMenus = array(
            'profile' => _p('profile'),
            'items' => _p('items'),
            'notifications' => _p('email_notifications'),
            'blocked' => _p('blocked_users')
        );
        if (Phpfox::getUserParam('user.hide_from_browse')) {
            $aMenus['invisible'] = _p('invisible_mode');
        }
        if (!Phpfox::isModule('privacy')) {
            unset($aMenus['items']);
        }

        $this->template()->buildPageMenu('js_privacy_block',
            $aMenus,
            array(
                'no_header_border' => true,
                'link' => $this->url()->makeUrl(Phpfox::getUserBy('user_name')),
                'phrase' => _p('view_your_profile')
            )
        );


        if ($this->request()->get('view') == 'blocked') {
            $this->template()->assign(array('bGoToBlocked' => true));
        }
        $this->template()->setTitle(_p('privacy_settings'))
            ->setBreadCrumb(_p('account'), $this->url()->makeUrl('profile'))
            ->setBreadCrumb(_p('privacy_settings'), $this->url()->makeUrl('user.privacy'), true)
            ->setFullSite()
            ->setHeader(array(
                    'privacy.css' => 'module_user'
                )
            )
            ->assign(array(
                'aForms' => $aUserPrivacy['privacy'],
                'aPrivacyNotifications' => $aNotifications,
                'aProfiles' => $aProfiles,
                'aUserPrivacy' => $aUserPrivacy,
                'aBlockedUsers' => Phpfox::getService('user.block')->get(),
                'aUserInfo' => $aUserInfo,
                'aItems' => $aItems
            ));

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
