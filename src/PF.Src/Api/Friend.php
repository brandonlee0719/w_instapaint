<?php

namespace Api;

class Friend extends \Core\Api
{
    /**
     * @param null $userId
     *
     * @return User\Object|User\Object[]
     * @throws \Exception
     */
    public function get($userId = null)
    {
        $this->auth();

        $params = [
            'limit' => 20,
        ];
        if (is_array($userId)) {
            $params = $userId;
            $userId = (isset($params['user_id']) ? $userId : user()->id);
        } else {
            $userId = ($userId === null ? user()->id : $userId);
        }

        $friends = [];
        $users = $this->db->select(\Phpfox::getUserField())
            ->from(':friend', 'f')
            ->join(':user', 'u', 'u.user_id = f.friend_user_id AND u.profile_page_id = 0')
            ->where(['f.user_id' => $userId])
            ->limit($params['limit'])
            ->all();

        foreach ($users as $user) {
            $friends[$user['user_id']] = new User\Object($user);
        }

        return $friends;
    }
}