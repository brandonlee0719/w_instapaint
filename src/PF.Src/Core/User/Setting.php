<?php

namespace Core\User;

use Phpfox;

class Setting extends \Core\Model
{
    private $_app;
    private static $_settings = null;

    public function __construct()
    {
        parent::__construct();

        self::$_settings = $this->cache->get('app_user_group_settings');
        if (!is_array(self::$_settings)) {
            $settings = [];
            // continue save to :user_group_custom any more?
            $rows = $this->db
                ->select('gs.*, us.user_group_id, us.value_actual')
                ->from(':user_group_setting', 'gs')
                ->join(':user_setting', 'us', 'gs.setting_id=us.setting_id')
                ->all();
            foreach ($rows as $row) {
                if (\Phpfox::isAppAlias($row['module_id'])) {
                    $settings[$row['user_group_id']][$row['name']] = $row['value_actual'];
                }
            }
            $this->cache->set('app_user_group_settings', $settings);
            self::$_settings = $this->cache->get('app_user_group_settings');
        }
    }

    public function get($key, $default = null, $userGroupId = null, $bRedirect = false)
    {

        if (!isset($this->active->group)) {
            return $default;
        }

        $userGroupId = ($userGroupId === null ? $this->active->group->id : $userGroupId);

        $value = Phpfox::getService('user.group.setting')->getGroupParam($userGroupId, $key, $default);

        if (!$value && $bRedirect) {
            Phpfox::redirectByPermissionDenied();
        }

        return $value;
    }

    public function save(\Core\App\Object $App, $settings)
    {
        $this->_app = $App;
        foreach ($settings as $group_id => $values) {
            foreach ($values as $key => $value) {
                $this->db->delete(':user_group_custom', ['user_group_id' => $group_id, 'module_id' => 'app_' . $this->_app->id, 'name' => $key]);
                $this->db->insert(':user_group_custom', [
                    'user_group_id' => $group_id,
                    'module_id'     => 'app_' . $this->_app->id,
                    'name'          => $key,
                    'default_value' => is_array($value) ? serialize($value) : (string)$value,
                ]);
            }
            Phpfox::getLib('cache')->remove('user_group_setting_' . $group_id);
        }

        $this->cache->del('app_user_group_settings');

        return true;
    }
}