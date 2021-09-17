<?php

namespace Api;

class User extends \Core\Api
{
    public function put($userId)
    {
        $requests = $this->accept([
            'name'     => 'full_name',
            'email'    => 'email',
            'username' => 'user_name',
        ]);

        $this->get($userId);

        \Phpfox::getService('user.process')->update($userId, $requests);

        return $this->get($userId);
    }

    public function post()
    {
        $this->requires([
            'name',
            'email',
            'password',
        ]);

        \Phpfox::getService('user.validate')->email($this->request('email'));

        $userId = \Phpfox::getService('user.process')->add([
            'full_name' => $this->request('name'),
            'email'     => $this->request('email'),
            'password'  => $this->request('password'),
        ]);

        if (!$userId) {
            throw new \Exception(implode('', \Phpfox_Error::get()));
        }

        return $this->get($userId);
    }

    /**
     * @param mixed $userId
     *
     * @return User\Object|User\Object[]
     * @throws \Exception
     */
    public function get($userId = null)
    {
        static $_user = [];

        if (is_array($userId)) {
            return new User\Object($userId);
        }

        if ($userId !== null && !$userId) {
            return new User\Object(false);
        }

        $where = [];
        if ($userId !== null) {
            $where = ['user_id' => $userId];

            if (!isset($_user[$userId])) {
                if (redis()->enabled()) {
                    $user = redis()->user($userId);
                } else {
                    $user = $this->db->select('*')->from(':user')->where($where)->get();
                }

                if (!isset($user['user_id'])) {
                    if (!$this->isApi()) {
                        return false;
                    }

                    throw new \Exception('User not found:' . $userId);
                }

                $_user[$userId] = $user;
            }

            $user = $_user[$userId];
        } else {
            $users = [];
            $rows = $this->db->select('*')->from(':user')
                ->where($this->getWhere())
                ->limit($this->getLimit(10))
                ->order($this->getOrder('user_id DESC'))
                ->all();
            foreach ($rows as $row) {
                $users[] = new User\Object($row);
            }

            return $users;
        }

        return new User\Object($user);
    }
}