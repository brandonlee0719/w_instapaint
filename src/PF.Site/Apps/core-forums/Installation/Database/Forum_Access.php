<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Access
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Access extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_access';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'access_id' => [
                'type' => 'int',
                'type_value' => '11',
                'other' => 'NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'forum_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_group_id' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL',
            ],
            'var_name' => [
                'type' => 'varchar',
                'type_value' => '150',
                'other' => 'NOT NULL',
            ],
            'var_value' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'forum_id' => ['forum_id', 'user_group_id'],
            'user_group_id' => ['user_group_id', 'var_name'],
        ];
    }
}