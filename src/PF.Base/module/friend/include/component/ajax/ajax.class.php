<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Module_Friend
 */
class Friend_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function getOnlineFriends()
    {
        Phpfox::getBlock('friend.mini');

        $this->call('$(\'#js_block_border_friend_mini\').find(\'.content:first\').html(\'' . $this->getContent() . '\');');
        if (!Phpfox::getParam('core.site_wide_ajax_browsing')) {
            $this->call('$Core.loadInit();');
        }
    }

    public function request()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $this->get('user_id'))) {
            $this->setTitle(_p('add_to_friends'));
        } else {
            $this->setTitle(_p('confirm_friend_request'));
        }

        Phpfox::getBlock('friend.request', array('user_id' => $this->get('user_id')));
        $this->call('<script>$Behavior.globalInit();</script>');
    }

    public function processRequest()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('friend')->isFriend($this->get('user_id'), Phpfox::getUserId())) {
            Phpfox::getService('friend.request.process')->delete($this->get('request_id'), $this->get('user_id'));
            $this->call(' $("#js_new_friend_request_' . $this->get('request_id') . '").remove();');

            return false;
        }

        $bProcessFromPanel = $this->get('inline');
        $bProcessFromManageAllRequests = $this->get('manage_all_request');
        $aVal = $this->get('val');

        if ($this->get('type') == 'yes') {
            if (Phpfox::getService('friend.process')->add(Phpfox::getUserId(), $this->get('user_id'),
                (isset($aVal['list_id']) ? (int)$aVal['list_id'] : 0))
            ) {
                if ($bProcessFromPanel) {
                    $aFriendFullName = Phpfox::getService('user')->getUser($this->get('user_id'), 'u.full_name');
                    $this->call(
                        vsprintf("\$Core.FriendRequest.panel.accept(%s, '%s');", [
                            $this->get('request_id'),
                            _p('you_and_full_name_are_now_friends', ['full_name' => $aFriendFullName['full_name']])
                        ])
                    );
                } elseif ($bProcessFromManageAllRequests) {
                    $aFriendFullName = Phpfox::getService('user')->getUser($this->get('user_id'), 'u.full_name');
                    $this->call(
                        vsprintf("\$Core.FriendRequest.manageAll.accept(%s, '%s');", [
                            $this->get('request_id'),
                            _p('you_and_full_name_are_now_friends', ['full_name' => $aFriendFullName['full_name']])
                        ])
                    );
                } else {
                    $sMess = _p('The request has been accepted successfully!');
                }
            }
        } else {
            if (Phpfox::getService('friend.process')->deny(Phpfox::getUserId(), $this->get('user_id'))) {
                if ($bProcessFromPanel) {
                    $this->call(sprintf("\$Core.FriendRequest.panel.deny(%s);", $this->get('request_id')));
                } elseif ($bProcessFromManageAllRequests) {
                    $this->call(sprintf("\$Core.FriendRequest.manageAll.deny(%s);", $this->get('request_id')));
                } else {
                    $sMess = _p('The request has been denied successfully!');
                }
            }
        }

        // update number of request in panel
        if ($bProcessFromPanel) {
            list(, $aFriends) = Phpfox::getService('friend.request')->get(0, 100);
            foreach ($aFriends as $key => $friend) {
                if ($friend['relation_data_id']) {
                    $sRelationShipName = Phpfox::getService('custom.relation')->getRelationName($friend['relation_id']);
                    if (isset($sRelationShipName) && !empty($sRelationShipName)) {
                        $aFriends[$key]['relation_name'] = $sRelationShipName;
                    } else {
                        //This relationship was removed
                        unset($aFriends[$key]);
                    }
                }
            }
            $iNumberFriendRequest = 0;
            foreach ($aFriends as $aFriend) {
                if (isset($aFriend['is_read']) && $aFriend['is_read'] == 1) {
                    continue;
                }
                $iNumberFriendRequest++;
            }
            if ($iNumberFriendRequest) {
                $this->call('$("span#js_total_new_friend_requests").html("' . $iNumberFriendRequest . '");');
            } else {
                $this->call('$("span#js_total_new_friend_requests").hide();');
            }
        } elseif ($bProcessFromManageAllRequests) {
        } else {
            // process in browse users page
            isset($sMess) && Phpfox::addMessage($sMess, $this->get('type') == 'yes' ? 'success' : 'warning');
            $this->reload();
        }

        return null;
    }

    public function addRequest()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('friend.can_add_friends', true);

        $aVals = $this->get('val');
        $aUser = Phpfox::getService('user')->getUser($aVals['user_id'],
            'u.user_id, u.user_name, u.user_image, u.server_id');

        if (Phpfox::getUserId() === $aUser['user_id']) {
            return false;
        } elseif (Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $aUser['user_id'])) {
            Phpfox_Error::set(_p('you_were_already_requested_to_be_friends'));
        } elseif (Phpfox::getService('friend.request')->isRequested($aUser['user_id'], Phpfox::getUserId())) {
            Phpfox_Error::set(_p('you_already_requested_to_be_friends'));
        } elseif (Phpfox::getService('friend')->isFriend($aUser['user_id'], Phpfox::getUserId())) {
            Phpfox_Error::set(_p('you_are_already_friends_with_this_user'));
        } else {
            if (Phpfox::getService('user.block')->isBlocked($aUser['user_id'], Phpfox::getUserId())) {
                $this->call('tb_remove();');

                return Phpfox_Error::set(_p('unable_to_send_a_friend_request_to_this_user_at_this_moment'));
            }
        }
        if (Phpfox_Error::isPassed() != true) {
            $this->call('tb_remove();');

            return false;
        }
        if (Phpfox::getService('friend.request.process')->add(Phpfox::getUserId(), $aVals['user_id'])) {
            if (isset($aVals['invite'])) {
                $this->call('tb_remove();')->html('#js_invite_user_' . $aVals['user_id'],
                    '' . _p('friend_request_successfully_sent') . '');
            } else {
                $this->call('$Core.submitFriendRequest();')
                    ->remove('#js_add_friend_on_profile');
            }

            $this->call('$(\'#js_parent_user_' . $aVals['user_id'] . '\').find(\'.user_browse_add_friend:first\').hide();')
                ->call('$(\'#js_user_tool_tip_cache_profile-' . $aVals['user_id'] . '\').closest(\'.js_user_tool_tip_holder:first\').remove();');

            if (isset($aVals['suggestion'])) {
                $this->loadSuggestion(false);
            }

            if (isset($aVals['page_suggestion'])) {
                $this->hide('#js_suggestion_parent_' . $aVals['user_id']);
            }
        }
        $this->remove('.add_as_friend_button');

        return null;
    }

    public function addList()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('friend.can_add_folders', true);

        $sName = $this->get('name');

        if (Phpfox::getLib('parse.format')->isEmpty($sName)) {
            $this->html('#js_friend_list_add_error', _p('provide_a_name_for_your_list'), '.show()');
            $this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
        } elseif (Phpfox::getService('friend.list')->reachedLimit()) // Did they reach their limit?
        {
            $this->html('#js_friend_list_add_error', _p('you_have_reached_your_limit'), '.show()');
            $this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
        } elseif (Phpfox::getService('friend.list')->isFolder($sName)) {
            $this->html('#js_friend_list_add_error', _p('folder_already_use'), '.show()');
            $this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
        } else {
            if ($iId = Phpfox::getService('friend.list.process')->add($sName)) {
                if ($this->get('custom')) {
                    $this->hide('#js_create_custom_friend_list')->show('#js_add_friends_to_list')->val('#js_custom_friend_list_id',
                        $iId);
                } else {
                    $this->call('js_box_remove($(\'#js_friend_list_add_error\'));');
                    $this->alert(_p('list_successfully_created'), _p('create_new_list'), 400, 150, true);
                    $this->call('$Core.reloadPage();');
                }
                $this->call('$Core.loadInit();');
            }
        }
    }

    public function editListName()
    {
        Phpfox::isUser(true);

        $sName = $this->get('name');
        $iListId = $this->get('id');

        if (Phpfox::getLib('parse.format')->isEmpty($sName)) {
            $this->html('#js_friend_list_edit_name_error', _p('provide_a_name_for_your_list'), '.show()');
            $this->call('$Core.processForm(\'#js_friend_list_edit_name_submit\', true);');
        } elseif (Phpfox::getService('friend.list')->isFolder($sName, $iListId)) {
            $this->html('#js_friend_list_edit_name_error', _p('folder_already_use'), '.show()');
            $this->call('$Core.processForm(\'#js_friend_list_edit_name_submit\', true);');
        } else {
            if (Phpfox::getService('friend.list.process')->update($iListId, $sName)) {
                $this->call('js_box_remove($(\'#js_friend_list_edit_name_error\'));');
                $this->alert(_p('list_successfully_edited'), _p('edit_list_name'), 400, 150, true);
                $this->call('$Core.reloadPage();');
            }
        }
    }

    public function addNewList()
    {
        $this->setTitle(_p('create_new_list'));

        Phpfox::getBlock('friend.list.add');
    }

    public function editName()
    {
        $this->setTitle(_p('edit_list_name'));

        Phpfox::getBlock('friend.list.edit-name');
    }

    public function buildCache()
    {
        $this->call('$Cache.friends = ' . json_encode(Phpfox::getService('friend')->getFromCache($this->get('allow_custom'))) . ';');
        $this->call('$Core.loadInit();');
    }

    public function getLiveSearch()
    {
        // This function is called from friend.static.search.js::getFriends in response to a key up event when is_mail is passed as true in building the template
        // parent_id we have to find the class "js_temp_friend_search_form" from its parents
        // search_for
        $aUsers = Phpfox::getService('friend')->getFromCache(false, $this->get('search_for'));

        if (empty($aUsers)) {
            return false;
        }
        // The next block is copied and modified from friend.static.search.js::getFriends
        $sHtml = '';
        $iFound = 0;
        $sStoreUser = '';
        foreach ($aUsers as $aUser) {
            $iFound++;
            if (substr($aUser['user_image'], 0, 5) == 'http:') {
                $aUser['user_image'] = '<img src="' . $aUser['user_image'] . '">';
            }
            $sHtml .= '<li><div rel="' . $aUser['user_id'] . '" class="js_friend_search_link ' . (($iFound == 1) ? 'js_temp_friend_search_form_holder_focus' : '') . '" href="#" onclick="return $Core.searchFriendsInput.processClick(this, \'' . $aUser['user_id'] . '\');"><span class="image">' . $aUser['user_image'] . '</span><span class="user">' . $aUser['full_name'] . '</span></div></li>';
            $sStoreUser .= '$Core.searchFriendsInput.storeUser(' . $aUser['user_id'] . ', JSON.parse(' . json_encode(json_encode($aUser)) . '));';

            if ($iFound > $this->get('total_search')) {
                break;
            }
        }
        $sHtml = '<div class="js_temp_friend_search_form_holder"><ul>' . $sHtml . '</ul></div>';
        $this->call($sStoreUser);
        $this->call('$("#' . $this->get('parent_id') . '").parent().find(".js_temp_friend_search_form").html(\'' . str_replace("'",
                "\\'", $sHtml) . '\').show();');
    }

    public function delete()
    {
        $bDeleted = $this->get('id') ? Phpfox::getService('friend.process')->delete($this->get('id')) : Phpfox::getService('friend.process')->delete($this->get('friend_user_id'),
            false);

        if ($bDeleted) {
            if ($this->get('reload')) {
                $this->call('window.location.href=window.location.href');

                return;
            }
            $this->call('$("#js_friend_' . $this->get('id') . '").remove();');
            $this->alert(_p('friend_successfully_removed'), _p('remove_friend'), 300, 150, true);
        }
    }

    public function search()
    {
        Phpfox::getBlock('friend.search', array(
            'input' => $this->get('input'),
            'friend_module_id' => $this->get('friend_module_id'),
            'friend_item_id' => $this->get('friend_item_id'),
            'type' => $this->get('type')
        ));
        if ($this->get('type') == 'mail') {
            $this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('search_for_members',
                    array('phpfox_squote' => true)) . '\');</script>');
        } else {
            $this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('search_for_your_friends',
                    array('phpfox_squote' => true)) . '\');</script>');
        }
    }

    public function searchAjax()
    {
        Phpfox::getBlock('friend.search', array(
            'search' => true,
            'friend_module_id' => $this->get('friend_module_id'),
            'friend_item_id' => $this->get('friend_item_id'),
            'page' => $this->get('page'),
            'find' => $this->get('find'),
            'letter' => $this->get('letter'),
            'input' => $this->get('input'),
            'view' => $this->get('view'),
            'type' => $this->get('type')
        ));

        $this->call('$(\'#js_friend_search_content\').html(\'' . $this->getContent() . '\');$Core.searchFriend.updateFriendsList();$Behavior.globalInit();');
    }

    public function searchDropDown()
    {
        Phpfox::isUser(true);
        $oDb = Phpfox_Database::instance();
        $sFind = $this->get('search');
        if (empty($sFind)) {
            $iCnt = 0;
        } else {
            list($iCnt, $aFriends) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId() . ' AND (u.full_name LIKE \'%' . Phpfox::getLib('parse.input')->convert($oDb->escape($sFind)) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))',
                'friend.time_stamp DESC', 0, 10, true, true);
        }

        if ($iCnt && isset($aFriends)) {
            $sHtml = '';
            foreach ($aFriends as $aFriend) {
                $sImage = Phpfox::getLib('image.helper')->display([
                    'user' => $aFriend,
                    'suffix' => '_50',
                    'no_link' => true
                ]);
                $sHtml .= '<li><a href="#" onclick="$(\'#' . $this->get('div_id') . '\').parent().hide(); $(\'#' . $this->get('input_id') . '\').val(\'' . $aFriend['user_id'] . '\'); $(\'#' . $this->get('text_id') . '\').val(\'' . addslashes(str_replace("O&#039;",
                        "'",
                        $aFriend['full_name'])) . '\'); return false;">' . $sImage . Phpfox::getLib('parse.output')->shorten(Phpfox::getLib('parse.output')->clean($aFriend['full_name']),
                        40, '...') . '</a></li>';
            }
            $this->html('#' . $this->get('div_id'), '<ul>' . $sHtml . '</ul>');
            $this->call('$(\'#' . $this->get('div_id') . '\').parent().show();');
        } else {
            $this->html('#' . $this->get('div_id'), '');
            $this->call('$(\'#' . $this->get('div_id') . '\').parent().hide();');
        }
    }

    public function loadSuggestion($bLoadTemplate = true)
    {
        Phpfox::getBlock('friend.suggestion', 'reload=true');

        if ($bLoadTemplate === true) {
            Phpfox_Template::instance()->getTemplate('friend.block.suggestion');
        }

        $this->slideUp('#js_friend_suggestion_loader')->html('#js_friend_suggestion',
            $this->getContent(false))->slideDown('#js_friend_suggestion');
        $this->call('$Core.loadInit();');
    }

    public function removeSuggestion()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('friend.suggestion')->remove($this->get('user_id'))) {
            if ($this->get('load')) {
                $this->loadSuggestion(false);
            }
        }
    }

    public function addFriendsToList()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('friend.list.process')->addFriendsToList((int)$this->get('list_id'),
            (array)$this->get('friends'))) {
            Phpfox::getBlock('privacy.friend', array('bNoCustomDiv' => true, 'list_id' => (int)$this->get('list_id')));

            $this->html('#js_custom_friend_list', $this->getContent(false));
        }
    }

    public function manageList()
    {
        Phpfox::isUser(true);

        if ($this->get('type') == 'add') {
            Phpfox::getService('friend.list.process')->addFriendsTolist($this->get('list_id'), $this->get('friend_id'));
        } else {
            Phpfox::getService('friend.list.process')->removeFriendsFromlist($this->get('list_id'),
                $this->get('friend_id'));
        }
    }

    public function setProfileList()
    {
        Phpfox::isUser(true);

        if ($this->get('type') == 'add') {
            if (Phpfox::getService('friend.list.process')->addListToProfile($this->get('list_id'))) {
                $this->call('$(\'.friend_list_display_profile\').parent().hide();');
                $this->call('$(\'.friend_list_remove_profile\').parent().show();');
                $this->alert(_p('successfully_added_this_list_to_your_profile'), _p('profile_friend_lists'), 300, 150,
                    true);
            }
        } else {
            if (Phpfox::getService('friend.list.process')->removeListFromProfile($this->get('list_id'))) {
                $this->call('$(\'.friend_list_display_profile\').parent().show();');
                $this->call('$(\'.friend_list_remove_profile\').parent().hide();');
            }
        }
    }

    public function updateListOrder()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('friend.list.process')->updateListOrder($this->get('list_id'), $this->get('friend_id'))) {
            $this->alert(_p('order_successfully_saved'), _p('list_order'), 400, 150, true);
            $this->call('$Core.processForm(\'#js_friend_list_order_form\', true);');
        }
    }

    public function viewMoreFriends()
    {
        Phpfox::getComponent('friend.index', array(), 'controller');
        $this->remove('.js_pager_view_more_link');
        $this->append('#js_view_more_friends', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function getMutualFriends()
    {
        Phpfox::isUser(true);
        if ((int)$this->get('page') == 0) {
            list($iCnt,) = Phpfox::getService('friend')->get(['friend.user_id' => Phpfox::getUserId()], 'friend.time_stamp DESC', '',
                '', true, false, false, $this->get('user_id'));
            $this->setTitle($iCnt == 1 ? _p('1_mutual_friend') : _p('total_mutual_friends', ['total' => $iCnt]));
        }
        Phpfox::getBlock('friend.mutual-browse');

        if ((int)$this->get('page') > 0) {
            $this->remove('#js_friend_mutual_browse_append_pager');
            $this->append('#js_friend_mutual_browse_append', $this->getContent(false));
        }
        //reload user profile, https://github.com/moxi9/phpfox/issues/546
        $this->call('<script>$Core.loadInit();$Behavior.globalInit();</script>');
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'accept':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (($aRequest = Phpfox::getService('friend.request')->getRequest($iId)) === false) {
                        continue;
                    }

                    Phpfox::getService('friend.process')->add(Phpfox::getUserId(), $aRequest['friend_user_id']);

                    $this->remove('.js_friend_request_' . $iId);
                }
                $this->updateCount();
                break;
            case 'deny':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (($aRequest = Phpfox::getService('friend.request')->getRequest($iId)) === false) {
                        continue;
                    }

                    Phpfox::getService('friend.process')->deny(Phpfox::getUserId(), $aRequest['friend_user_id']);

                    $this->remove('.js_friend_request_' . $iId);
                }
                break;
        }

        $this->hide('.moderation_process');
    }

    public function removePendingRequest()
    {
        $iId = $this->get('id');
        if (Phpfox::getService('friend.request.process')->delete($iId, Phpfox::getUserId())) {
            $this->call('$Core.reloadPage();');
        }
    }

    public function denyRequest()
    {
        if (Phpfox::getService('friend.process')->deny(Phpfox::getUserId(), $this->get('user_id'))) {
            $this->call('$Core.reloadPage();');
        }
    }

    public function browseOnline()
    {
        $this->setTitle(_p('friends_online'));
        Phpfox::getBlock('friend.browse-online');
    }
}
