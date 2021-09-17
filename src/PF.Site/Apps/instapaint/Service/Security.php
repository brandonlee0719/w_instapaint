<?php

namespace Apps\Instapaint\Service;

class Security extends \Phpfox_Service
{
    // Security constants
    const VISITOR_GROUP_ID = 3; // Id of group "Visitor"
    const CLIENT_GROUP_ID = 6; // Id of group "Client"
    const PAINTER_GROUP_ID = 7; // Id of group "Painter"
    const APPROVED_PAINTER_GROUP_ID = 8; // Id of group "Approved Painter"
    const ADMIN_GROUP_ID = 9; // Id of group "Admin"
    const DENIED_REDIRECT_ROUTE = 'subscribe'; // Redirection route for denied access
    const MY_DASHBOARD_MENU_ID = 40; // Id of "My Dashboard" menu
    const CUSTOM_FIELDS = [ // These are the Ids the code expects in the database for the custom fields
        'user_type' => [
            'id' => 2,
            'options' => [
                'client' => [
                    'id' => 1
                ],
                'painter' => [
                    'id' => 2
                ]
            ]
        ]
    ];

    /**
     * Restricts access to users who are not supposed to visit
     * specific sections of the site provided by the Instapaint app,
     * and redirects those users if necessary.
     *
     * It only allows access to users in the user group ids provided in
     * the $groupIds parameter.
     *
     * @param array $allowedGroupIds The group ids allowed to access
     * @param string $redirectRoute Redirect denied users to this route
     * @return null
     */
    public function allowAccess($allowedGroupIds, $redirectRoute = self::DENIED_REDIRECT_ROUTE)
    {
        // Get current user group id
        try {
            $userGroupId = user()->group->id;
        } catch (\Exception $e) {
            // If there's an error getting the user id, assume user is visitor
            $userGroupId = self::VISITOR_GROUP_ID;
        }

        /*
         * If current user group id is not found in the allowed group ids,
         * redirect to $redirectRoute.
         */
        if (!in_array($userGroupId, $allowedGroupIds)) {
            url()->send($redirectRoute);
        }
    }

    /**
     * Returns the route to the dashboard corresponding to the current user,
     * or an empty string if no dashboard corresponds to such user.
     *
     * @return string The dashboard route or an empty string
     */
    public function getUserDashboardRoute()
    {
        $userGroupId = $this->getUserGroupId();

        if ($userGroupId == self::CLIENT_GROUP_ID) {
            return 'client-dashboard';
        } else if ($userGroupId == self::PAINTER_GROUP_ID || $userGroupId == self::APPROVED_PAINTER_GROUP_ID) {
            return 'painter-dashboard';
        } else if ($userGroupId == self::ADMIN_GROUP_ID) {
            return 'admin-dashboard';
        } else {
            return '';
        }
    }

    /**
     * Returns the id of the group which the current user belongs to,
     * if there's an exception assume the user is a visitor.
     *
     * @return int The user group id
     */
    public function getUserGroupId()
    {
        // Get current user group id
        try {
            $userGroupId = user()->group->id;
        } catch (\Exception $e) {
            // If there's an error getting the user id, assume user is visitor
            $userGroupId = self::VISITOR_GROUP_ID;
        }
        return $userGroupId;
    }

    public function getCSRFToken() {
        return $this->request()->getIdHash();
    }

    public function checkCSRFToken($token) {
        return $token == $this->request()->getIdHash();
    }

    public function friendWithAdmins($userId) {
        $admins = db()->select('user_id')
            ->from(':user')
            ->where(['user_group_id' => $this::ADMIN_GROUP_ID])
            ->executeRows();

        foreach ($admins as $admin) {
            db()->insert(':friend', ['user_id' => $userId, 'friend_user_id' => $admin['user_id'], 'time_stamp' => time()]);
            db()->insert(':friend', ['user_id' => $admin['user_id'], 'friend_user_id' => $userId, 'time_stamp' => time()]);
        }
    }
}
