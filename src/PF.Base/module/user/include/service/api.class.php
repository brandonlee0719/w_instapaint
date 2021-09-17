<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Api
 */
class User_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'user_id',
            'user_name',
            'full_name',
            'user_image',
            'is_friend',
            'is_friend_of_friend',
            'is_friend_request'
        ]);
        $this->setGeneralFields([
            'user_group_id',
            'gender',
            'birthday',
            'birthday_search',
            'title',
            'is_online',
            'email',
            'cover_photo_exists',
            'relation_id',
            'relation_with_id',
            'relation_phrase'


        ]);
        $this->setFullFields([
            'country_iso',
            'language_id',
            'time_zone'
        ]);
    }

    /**
     * @description: get info of a user
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        $aUser = Phpfox::getService('user')->get($params['id'], true, false);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        $iUserId = $aUser['user_id'];
        if (Phpfox::getService('user.block')->isBlocked(null, $iUserId))
        {
            return $this->error(_p('Sorry, information of this user isn\'t available for you.'));
        }

        $aRelation = Phpfox::getService('custom.relation')->getLatestForUser($params['id'], null, true);
        $aUser['relation_id'] = (empty($aRelation) && empty($aRelation['relation_id'])) ? null : $aRelation['relation_id'];
        $aUser['relation_with_id'] = (empty($aRelation) && empty($aRelation['with_user_id'])) ? null : $aRelation['with_user_id'];
        $aUser['relation_phrase'] = (empty($aRelation) && empty($aRelation['phrase_var_name'])) ? null : _p($aRelation['phrase_var_name']);
        $mode = 'public';
        if (!Phpfox::getParam('core.friends_only_community') || $aUser['is_friend'])
        {
            $mode = 'general';
        }
        if (Phpfox::isAdmin() || Phpfox::getUserParam('core.can_view_private_items') || $aUser['user_id'] == Phpfox::getUserId() || !empty($params['is_edit']))
        {
            $mode = 'full';
        }

        $aItem = $this->getItem($aUser, $mode);
        return $this->success($aItem, $messages);
    }

    /**
     * @description: update info of a user
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $aUser = Phpfox::getService('user')->get($params['id'], true);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        $iUserId = $aUser['user_id'];
        if (!Phpfox::isAdmin() && Phpfox::getUserId() != $iUserId && !Phpfox::getUserParam('user.can_edit_users'))
        {
            return $this->error(_p('You don\'t have permission to edit this {{ item }}.', ['item' => _p('user__l')]));
        }

        $aValidation = [];
        $aVals = Phpfox_Request::instance()->getArray('val');

        if (isset($aVals['email']) && $aUser['email'] != $aVals['email'])
        {
            if (Phpfox::getUserParam('user.can_change_email') || Phpfox::getUserParam('user.can_edit_users'))
            {
                $aValidation['email'] = array(
                    'def' => 'email',
                    'title' => _p('provide_a_valid_email_address')
                );
            }
            else
            {
                return $this->error(_p('You cannot change the Email Address of this user.'));
            }
        }

        if (isset($aVals['full_name']))
        {
            if (Phpfox::getUserParam('user.can_change_own_full_name') || Phpfox::getUserParam('user.can_edit_users'))
            {
                $aValidation['full_name'] = _p('provide_your_full_name');
            }
            else
            {
                return $this->error(_p('You cannot change the Full Name of this user.'));
            }

        }
        if (isset($aVals['user_name']))
        {
            if ((!Phpfox::getParam('user.profile_use_id') && Phpfox::getUserParam('user.can_change_own_user_name')) || Phpfox::getUserParam('user.can_edit_users'))
            {
                $aValidation['user_name'] = array('def' => 'username', 'title' => _p('provide_a_user_name'));
            }
            else
            {
                return $this->error(_p('You cannot change the Username of this user.'));
            }
        }

        $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

        if ($oValid->isValid($aVals))
        {
            if (setting('user.disable_username_on_sign_up') != 'username' && setting('user.split_full_name') && (!empty($aVals['first_name']) || !empty($aVals['last_name'])))
            {
                if (empty($aVals['first_name']) || empty($aVals['last_name']))
                {
                    return $this->error(_p('please_fill_in_both_your_first_and_last_name'));
                }
                $aVals['full_name'] = $aVals['first_name'] . ' ' . $aVals['last_name'];
            }

            if (!empty($aVals['password']))
            {
                if (!Phpfox::getService('user.process')->updatePassword(['new_password' => $aVals['password']], $iUserId, false))
                {
                    return $this->error(_p('Cannot change password for this user.'), true);
                }
            }

            if (!empty($aVals['email']) && (Phpfox::getUserParam('user.can_change_email') || Phpfox::getUserParam('user.can_edit_users')) && $aUser['email'] != $aVals['email'])
            {
                $bAllowed = Phpfox::getService('user.verify.process')->changeEmail($aUser, $aVals['email']);
                if (is_string($bAllowed))
                {
                    return $this->error($bAllowed);
                }
            }

            if (empty($aVals['full_name']))
            {
                $aVals['full_name'] = $aUser['full_name'];
            }

            if (!empty($aVals['delete_image']))
            {
                if ($iUserId != Phpfox::getUserId() && !Phpfox::getUserParam('user.can_change_other_user_picture'))
                {
                    return $this->error(_p('You don\'t have permission to change other user profile photo.'));
                }
                Phpfox::getService('user.process')->removeProfilePic($iUserId);
                storage()->del('user/avatar/' . $iUserId);
            }

            if (!empty($_FILES['image']))
            {
                if ($iUserId != Phpfox::getUserId() && !Phpfox::getUserParam('user.can_change_other_user_picture'))
                {
                    return $this->error(_p('You don\'t have permission to change other user profile photo.'));
                }
                $aImage = Phpfox_File::instance()->load('image', array('jpg', 'gif', 'png'), (Phpfox::getUserParam('user.max_upload_size_profile_photo') === 0 ? null : (Phpfox::getUserParam('user.max_upload_size_profile_photo') / 1024)));

                if ($aImage !== false)
                {
                    Phpfox::getService('user.process')->uploadImage($iUserId, true, null, true);
                }
            }

            Phpfox::getService('user.process')->update($iUserId, $aVals, array(
                    'changes_allowed' => Phpfox::getUserParam('user.total_times_can_change_user_name'),
                    'total_user_change' => $aUser['total_user_change'],
                    'full_name_changes_allowed' => Phpfox::getUserParam('user.total_times_can_change_own_full_name'),
                    'total_full_name_change' => $aUser['total_full_name_change'],
                    'current_full_name' => $aUser['full_name'],
                    'is_api' => true
                ));

            $params['is_edit'] = true;
        }

        return $this->get($params, [_p('{{ item }} successfully updated.', ['item' => _p('user')])]);
    }

    /**
     * @description: delete a user
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $aUser = Phpfox::getService('user')->get($params['id'], true);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        $iUserId = $aUser['user_id'];

        if ($iUserId != Phpfox::getUserId() && !(Phpfox::getUserParam('admincp.has_admin_access') && Phpfox::getUserParam('user.can_delete_others_account')))
        {
            return $this->error(_p('You cannot {{ action }} this {{ item }}.', ['action' => _p('delete__l'), 'item' => _p('user__l')]));
        }
        if (Phpfox::getService('user')->isAdminUser($iUserId, false))
        {
            return $this->error(_p('you_are_unable_to_delete_a_site_administrator'));
        }

        Phpfox::massCallback('onDeleteUser', $iUserId);
        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('user')])]);
    }

    public function post()
    {
        if (!Phpfox::getParam('user.allow_user_registration'))
        {
            return $this->error(_p('Sorry, you cannot register an account now.'));
        }

        if (Phpfox::isUser())
        {
            return $this->error(_p('You cannot register an account with an access token of an user.'));
        }

        $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => Phpfox::getService('user.register')->getValidation(null, true)));

        $aVals = $this->request()->getArray('val');
        if (Phpfox::isModule('invite') && Phpfox::getService('invite')->isInviteOnly())
        {
            if (!Phpfox::getService('invite')->isValidInvite($aVals['email']))
            {
                return $this->error();
            }
        }

        if (!Phpfox::getParam('user.profile_use_id') && (Phpfox::getParam('user.disable_username_on_sign_up') != 'full_name'))
        {
            $aVals['user_name'] = str_replace(' ', '-', $aVals['user_name']);
//            $aVals['user_name'] = str_replace('_', '-', $aVals['user_name']);
            Phpfox::getService('user.validate')->user($aVals['user_name']);
        }

        Phpfox::getService('user.validate')->email($aVals['email']);

        if ($oValid->isValid($aVals))
        {
            if ($iId = Phpfox::getService('user.process')->add($aVals))
            {
                \Phpfox::getService('user.auth')->setUserId($iId);
                return $this->get(['id' => $iId], [_p('{{ item }} successfully added.', ['item' => _p('user')])]);
            }
        }
        return $this->error();
    }

    /**
     * @description: get users
     * @return array|int|mixed|string
     */
    public function gets()
    {
        if (!Phpfox::getUserParam('user.can_browse_users_in_public'))
        {
            return $this->error(_p('You don\'t have permission to browse {{ items }}.', ['items' => _p('users__l')]));
        }
        $this->initSearchParams();
        $view = $this->request()->get('view');
        $users = [];
        switch ($view) {
            case 'recommend':
                if (Phpfox::isUser())
                {
                    if (Phpfox::isModule('friend'))
                    {
                        $users = Phpfox::getService('friend.suggestion')->get();
                    }
                }
                break;
            case 'recent':
                $users = Phpfox::getService('user.featured')->getRecentActiveUsers();
                break;
            default:
                $aPages = [$this->getSearchParam('limit')];
                $aDisplays = [];
                foreach ($aPages as $iPageCnt)
                {
                    $aDisplays[$iPageCnt] = _p('per_page', ['total' => $iPageCnt]);
                }

                $aSorts = [
                    'u.full_name' => _p('name'),
                    'u.joined' => _p('joined'),
                    'u.last_login' => _p('last_login')
                ];

                $aAge = array();
                for ($i = Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_end'))); $i <= Phpfox::getService('user')->age(Phpfox::getService('user')->buildAge(1, 1, Phpfox::getParam('user.date_of_birth_start'))); $i++)
                {
                    $aAge[$i] = $i;
                }

                $iYear = date('Y');

                $aGenders = Phpfox::getService('core')->getGenders();
                $aGenders[''] = (count($aGenders) == '2' ? _p('both') : _p('all'));

                $sDefaultOrderName = 'u.full_name';
                $sDefaultSort = 'ASC';
                if (Phpfox::getParam('user.user_browse_default_result') == 'last_login')
                {
                    $sDefaultOrderName = 'u.last_login';
                    $sDefaultSort = 'DESC';
                }

                $iDisplay = $this->getSearchParam('limit');
                $aFilters = array(
                    'display' => array(
                        'type' => 'select',
                        'options' => $aDisplays,
                        'default' => $iDisplay
                    ),
                    'sort' => array(
                        'type' => 'select',
                        'options' => $aSorts,
                        'default' => $sDefaultOrderName
                    ),
                    'sort_by' => array(
                        'type' => 'select',
                        'options' => array(
                            'DESC' => _p('descending'),
                            'ASC' => _p('ascending')
                        ),
                        'default' => $sDefaultSort
                    ),
                    'keyword' => array(
                        'type' => 'input:text',
                        'size' => 15,
                        'class' => 'txt_input'
                    ),
                    'type' => array(
                        'type' => 'select',
                        'options' => array(
                            '0' => array(_p('email_name'), 'AND ((u.full_name LIKE \'%[VALUE]%\' OR (u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\')))'),
                            '1' => array(_p('email'), 'AND ((u.email LIKE \'%[VALUE]@%\' OR u.email = \'[VALUE]\'))'),
                            '2' => array(_p('name'), 'AND (u.full_name LIKE \'%[VALUE]%\')')
                        ),
                        'depend' => 'keyword'
                    ),
                    'gender' => array(
                        'type' => 'input:radio',
                        'options' => $aGenders,
                        'default_view' => '',
                        'search' => 'AND u.gender = \'[VALUE]\'',
                        'suffix' => '<br />'
                    ),
                    'from' => array(
                        'type' => 'select',
                        'options' => $aAge,
                        'select_value' => _p('from')
                    ),
                    'to' => array(
                        'type' => 'select',
                        'options' => $aAge,
                        'select_value' => _p('to')
                    ),
                    'country' => array(
                        'type' => 'select',
                        'options' => Phpfox::getService('core.country')->get(),
                        'search' => 'AND u.country_iso = \'[VALUE]\'',
                        'add_any' => true,
                        // 'style' => 'width:150px;',
                        'id' => 'country_iso'
                    ),
                    'country_child_id' => array(
                        'type' => 'select',
                        'search' => 'AND ufield.country_child_id = \'[VALUE]\'',
                        'clone' => true
                    ),
                    'city' => array(
                        'type' => 'input:text',
                        'size' => 15,
                        'search' => 'AND ufield.city_location LIKE \'%[VALUE]%\''
                    ),
                    'zip' => array(
                        'type' => 'input:text',
                        'size' => 10,
                        'search' => 'AND ufield.postal_code = \'[VALUE]\''
                    ),
                    'show' => array(
                        'type' => 'select',
                        'options' => array(
                            '1' => _p('name_and_photo_only'),
                            '2' => _p('name_photo_and_users_details')
                        ),
                        'default_view' => (Phpfox::getParam('user.user_browse_display_results_default') == 'name_photo_detail' ? '2' : '1')
                    )
                );

                if (!Phpfox::getUserParam('user.can_search_by_zip'))
                {
                    unset ($aFilters['zip']);
                }

                $aSearchParams = array(
                    'type' => 'browse',
                    'filters' => $aFilters,
                    'search' => 'keyword',
                    'custom_search' => true,
                    'no_session_search' => true
                );

                $oFilter = Phpfox_Search::instance()->set($aSearchParams);

                $aCustomSearch = $oFilter->getCustom();
                $bIsGender = false;

                if (($iFrom = $oFilter->get('from')) || ($iFrom = $this->request()->getInt('from')))
                {
                    $oFilter->setCondition('AND u.birthday_search <= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iFrom). '\'' . ' AND ufield.dob_setting IN(0,1,2)');
                    $bIsGender = true;
                }
                if (($iTo = $oFilter->get('to')) || ($iTo = $this->request()->getInt('to')))
                {
                    $oFilter->setCondition('AND u.birthday_search >= \'' . Phpfox::getLib('date')->mktime(0, 0, 0, 1, 1, $iYear - $iTo) .'\'' . ' AND ufield.dob_setting IN(0,1,2)');
                    $bIsGender = true;
                }

                if (($sLocation = $this->request()->get('location')))
                {
                    $oFilter->setCondition('AND u.country_iso = \'' . Phpfox_Database::instance()->escape($sLocation) . '\'');
                }

                if (($sGender = $this->request()->getInt('gender')))
                {
                    $oFilter->setCondition('AND u.gender = \'' . Phpfox_Database::instance()->escape($sGender) . '\'');
                }

                if (($sLocationChild = $this->request()->getInt('state')))
                {
                    $oFilter->setCondition('AND ufield.country_child_id = \'' . Phpfox_Database::instance()->escape($sLocationChild) . '\'');
                }

                if (($sLocationCity = $this->request()->get('city-name')))
                {
                    $oFilter->setCondition('AND ufield.city_location = \'' . Phpfox_Database::instance()->escape(Phpfox::getLib('parse.input')->convert($sLocationCity)) . '\'');
                }

                $oFilter->setCondition('AND u.status_id = 0 AND u.view_id = 0 AND u.profile_page_id = 0');
                if (Phpfox::isUser()) {
                    $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
                    if (!empty($aBlockedUserIds)) {
                        $oFilter->setCondition('AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')');
                    }
                }

                $iPageSize = $oFilter->getDisplay();

                list(, $users) = Phpfox::getService('user.browse')->conditions($oFilter->getConditions())
                    ->sort($oFilter->getSort())
                    ->page($oFilter->getPage())
                    ->limit($iPageSize)
                    ->custom($aCustomSearch)
                    ->gender($bIsGender)
                    ->get();

        }

        foreach ($users as $key => $user) {
            foreach ($user as $field => $data) {
                if (!in_array($field, $this->_publicFields))
                {
                    unset($users[$key][$field]);
                }
            }
        }

        return $this->success($users);
    }

    /**
     * @description: get custom fields info of a user
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function getCustom($params, $messages = [])
    {
        if (!Phpfox::isModule('custom'))
        {
            return $this->error(_p('The request is invalid.'));
        }
        $aUser = Phpfox::getService('user')->get($params['id'], true, false);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        $iUserId = $aUser['user_id'];
        if (Phpfox::getService('user.block')->isBlocked(null, $iUserId))
        {
            return $this->error(_p('Sorry, information of this user isn\'t available for you.'));
        }

        if (!Phpfox::getParam('core.friends_only_community') || $aUser['is_friend'] || Phpfox::isAdmin() || Phpfox::getUserParam('core.can_view_private_items') || $aUser['user_id'] == Phpfox::getUserId())
        {
            $result = [];
            $fields = Phpfox::getService('custom')->getForEdit(array('user_main', 'user_panel', 'profile_panel'), $iUserId, Phpfox::getUserBy('user_group_id'), false, $iUserId);
            foreach ($fields as $field)
            {
                if (empty($field['value']) && !empty($field['options']) && is_array($field['options'])) {
                    $values = [];
                    foreach ($field['options'] as $option)
                    {
                        if (!empty($option['selected']))
                        {
                            $values[] = $option['value'];
                        }
                    }
                    $field['value'] = implode(", ", $values);

                }
                $group = Phpfox::getService('custom.group')->getGroup($field['group_id']);
                $result[] = [
                    'field_id' => $field['field_id'],
                    'field_name' => $field['field_name'],
                    'field_label' => _p($field['phrase_var_name']),
                    'var_type' => $field['var_type'],
                    'value' => isset($field['value']) ? $field['value'] : '',
                    'custom_value' => isset($field['customValue']) ? $field['customValue'] : '',
                    'group_id' => $group['group_id'],
                    'group_label' => _p($group['phrase_var_name']),
                ];
            }

            return $this->success($result, $messages);
        }
        return $this->error(_p('You don\'t have permission to view custom fields info of this user.'));
    }

    /**
     * @description: update custom fields of a user
     * @param $params
     *
     * @return array|bool
     */
    public function putCustom($params)
    {
        if (!Phpfox::isModule('custom'))
        {
            return $this->error(_p('The request is invalid.'));
        }
        $aUser = Phpfox::getService('user')->get($params['id'], true, false);
        if (empty($aUser) || empty($aUser['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
        }

        $iUserId = $aUser['user_id'];

        if (!(Phpfox::isAdmin() || (Phpfox::getUserId() == $iUserId && Phpfox::getUserParam('custom.can_edit_own_custom_field')) || (Phpfox::getUserId() != $iUserId && Phpfox::getUserParam('custom.can_edit_other_custom_fields'))))
        {
            return $this->error(_p('You don\'t have permission to edit custom fields of this user.'));
        }

        $aCustoms = Phpfox_Request::instance()->getArray('custom');

        if (!empty($aCustoms))
        {
            Phpfox::getService('custom.process')->updateFields($iUserId, $iUserId, $aCustoms);
        }

        return $this->getCustom($params, [_p('{{ item }} successfully updated.', ['item' => _p('Custom fields')])]);

    }

    /**
     * @description: Get current user
     * @return array|bool
     */
    public function getMine()
    {
        $this->isUser();
        return $this->get(['id' => Phpfox::getUserId()]);
    }
}
