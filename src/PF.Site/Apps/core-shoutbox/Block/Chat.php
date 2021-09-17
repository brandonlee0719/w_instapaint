<?php
namespace Apps\phpFox_Shoutbox\Block;

use Phpfox_Component;
use Phpfox;
use User_Service_User;
use Apps\phpFox_Shoutbox\Service\Shoutbox as sb;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Chat
 * @author  Neil <neil@phpfox.com>
 * @package Apps\phpFox_Shoutbox\Block
 */
class Chat extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        //Global config
        if (!user('shoutbox_can_view')) {
            return false;
        }
        $bIsAdmin = Phpfox::isAdmin();
        $bCanShare = (user('shoutbox_can_share') && Phpfox::isUser());
        $aParentModule = [];
        //On Pages or Groups
        if (defined("PHPFOX_PAGES_ITEM_TYPE")) {
            $aParentModule = $this->getParam('aParentModule');
            if (PHPFOX_PAGES_ITEM_TYPE == 'pages') {
                if (!setting('shoutbox_enable_pages')) {
                    return false;
                }
                $aParentModuleInfo = Phpfox::getService('pages')->getPage($aParentModule['item_id']);
                if (!$bIsAdmin) {
                    if (Phpfox::getService('pages')->isAdmin($aParentModuleInfo)) {
                        $bIsAdmin = true;
                    }
                }
                //In pages, check can view shoutbox
                if (!Phpfox::getService('pages')->hasPerm($aParentModule['item_id'], 'shoutbox.view_shoutbox')) {
                    return false;
                }
                //In pages, check can share shoutbox
                if (!Phpfox::getService('pages')->hasPerm($aParentModule['item_id'], 'shoutbox.share_shoutbox')) {
                    $bCanShare = false;
                }
            } elseif (PHPFOX_PAGES_ITEM_TYPE == 'groups') {
                if (!setting('shoutbox_enable_groups')) {
                    return false;
                }
                $aParentModuleInfo = Phpfox::getService('groups')->getPage($aParentModule['item_id']);
                if (!$bIsAdmin) {
                    if (Phpfox::getService('groups')
                              ->isAdmin($aParentModuleInfo)
                    ) {
                        $bIsAdmin = true;
                    }
                }
                //In pages, check can view shoutbox
                if (!Phpfox::getService('groups')->hasPerm($aParentModule['item_id'], 'shoutbox.view_shoutbox')) {
                    return false;
                }
                //In pages, check can share shoutbox
                if (!Phpfox::getService('groups')->hasPerm($aParentModule['item_id'], 'shoutbox.share_shoutbox')) {
                    $bCanShare = false;
                }
            }
            
        } else {//On Index
            if (!setting('shoutbox_enable_index')) {
                return false;
            }
        }
        $sModuleId = (isset($aParentModule['module_id'])) ? $aParentModule['module_id'] : 'index';
        $iItemId = (isset($aParentModule['item_id'])) ? $aParentModule['item_id'] : '0';
        $aShoutboxes = sb::get()->getShoutboxes($sModuleId, $iItemId);
        $aUser = User_Service_User::instance()
                                  ->getUser(Phpfox::getUserId());
        if (isset($aShoutboxes[0])) {
            Phpfox::removeCookie('last_shoutbox_id');
            Phpfox::setCookie('last_shoutbox_id', $aShoutboxes[0]['shoutbox_id']);
        }
        $this->template()
            ->assign([
                'sHeader'     => _p('shoutbox'),
                'aShoutboxes' => $aShoutboxes,
                'aUser'       => $aUser,
                'aIsAdmin'    => $bIsAdmin,
                'sModuleId'   => $sModuleId,
                'iItemId'     => $iItemId,
                'iUserId'     => Phpfox::getUserId(),
                'bCanShare'   => $bCanShare
            ]);
        return 'block';
    }
}