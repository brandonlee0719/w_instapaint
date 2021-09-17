<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Subscribe
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Subscribe extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_subscribe';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'subscribe_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'thread_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],

        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'thread_id' => ['thread_id', 'user_id'],
        ];
    }
}