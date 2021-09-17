<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Block_Info
 */
class Profile_Component_Block_Info extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'profile.basic_info')) {
            return false;
        }
        $aUser['bRelationshipHeader'] = true;
        $sRelationship = Phpfox::getService('custom')->getRelationshipPhrase($aUser);
        $aUserDetails = array();
        if (!empty($aUser['gender'])) {
            $aUserDetails[_p('gender')] = '<a href="' . $this->url()->makeUrl('user.browse',
                    array('gender' => $aUser['gender'])) . '">' . $aUser['gender_name'] . '</a>';
        }

        $aUserDetails = array_merge($aUserDetails, $aUser['birthdate_display']);

        $sExtraLocation = '';

        if (!empty($aUser['city_location'])) {
            $sExtraLocation .= '<a href="' . $this->url()->makeUrl('user.browse', array(
                    'location' => $aUser['country_iso'],
                    'state' => $aUser['country_child_id'],
                    'city-name' => $aUser['city_location']
                )) . '">' . Phpfox::getLib('parse.output')->clean($aUser['city_location']) . '</a> &raquo;';
        }

        if ($aUser['country_child_id'] > 0 && $sChild = Phpfox::getService('core.country')->getChild($aUser['country_child_id'])) {
            $sExtraLocation .= '<a href="' . $this->url()->makeUrl('user.browse', array(
                    'location' => $aUser['country_iso'],
                    'state' => $aUser['country_child_id']
                )) . '">' . $sChild . '</a> &raquo;';
        }

        if (!empty($aUser['country_iso']) && Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'],
                'profile.view_location')) {
            $aUserDetails[_p('location')] = $sExtraLocation . '<a href="' . $this->url()->makeUrl('user.browse',
                    array('location' => $aUser['country_iso'])) . '">' . Phpfox::getPhraseT($aUser['location'],
                    'country') . '</a>';
        }

        if ((int)$aUser['last_login'] > 0 && ((!$aUser['is_invisible']) || (Phpfox::getUserParam('user.can_view_if_a_user_is_invisible') && $aUser['is_invisible']))) {
            $aUserDetails[_p('last_login')] = Phpfox::getLib('date')->convertTime($aUser['last_login'],
                'core.profile_time_stamps');
        }

        if ((int)$aUser['joined'] > 0) {
            $aUserDetails[_p('member_since')] = Phpfox::getLib('date')->convertTime($aUser['joined'],
                'core.profile_time_stamps');
        }

        if (Phpfox::getUserGroupParam($aUser['user_group_id'], 'profile.display_membership_info')) {
            $aUserDetails[_p('membership')] = (empty($aUser['icon_ext']) ? '' : '<img src="' . Phpfox::getParam('core.url_icon') . $aUser['icon_ext'] . '" class="v_middle" alt="' . Phpfox_Locale::instance()->convert($aUser['title']) . '" title="' . Phpfox_Locale::instance()->convert($aUser['title']) . '" /> ') . $aUser['prefix'] . Phpfox_Locale::instance()->convert($aUser['title']) . $aUser['suffix'];
        }

        $aUserDetails[_p('profile_views')] = $aUser['total_view'];

        if (Phpfox::isModule('rss') && Phpfox::getParam('rss.display_rss_count_on_profile') && Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'],
                'rss.display_on_profile')) {
            $aUserDetails[_p('rss_subscribers')] = (Phpfox::getUserId() == $aUser['user_id']) ? '<a href="#" onclick="tb_show(\'' . _p('rss_subscribers_log') . '\', $.ajaxBox(\'rss.log\', \'height=500&amp;width=500&amp\')); return false;">' . $aUser['rss_count'] . '</a>' : $aUser['rss_count'];
        }

        $sEditLink = '';
        if ($aUser['user_id'] == Phpfox::getUserId()) {
            $sEditLink = '<div class="js_edit_header_bar">';
            $sEditLink .= '<span id="js_user_basic_info" style="display:none;"><img src="' . $this->template()->getStyle('image',
                    'ajax/small.gif') . '" alt="" class="v_middle" /></span>';
            $sEditLink .= '<a href="' . Phpfox_Url::instance()->makeUrl('user.profile') . '" id="js_user_basic_edit_link" class="btn btn-primary">';
            $sEditLink .= '<i class="ico ico-textedit mr-1"></i>' . _p('update_profile_info');
            $sEditLink .= '</a>';
            $sEditLink .= '</div>';
        }
        // Get the Smoker and Drinker details
        $bShowCustomFields = $this->getParam('show_custom_fields', true);


        // Add email if current user is an admin:

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        if (user()->group->id == $securityService::ADMIN_GROUP_ID) {
            $aUserDetails['Email (only admins can see it)'] = '<a href="mailto:' . $aUser['email'] . '">' . $aUser['email'] . '</a>';
        }

        $this->template()->assign(array(
                'aUserDetails' => $aUserDetails,
                'sBlockJsId' => 'profile_basic_info',
                'sRelationship' => trim($sRelationship),
                'bShowCustomFields' => $bShowCustomFields,
            )
        );

        (($sPlugin = Phpfox_Plugin::get('profile.component_block_info')) ? eval($sPlugin) : false);

        $this->template()->assign(array(
                'sHeader' => $sEditLink . _p('basic_info'),
                'sEditLink' => $sEditLink
            )
        );
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_block_info_clean')) ? eval($sPlugin) : false);
    }
}
