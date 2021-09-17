<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Thread
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Thread extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_thread';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'thread_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'forum_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'group_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'poll_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'start_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'is_announcement' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_closed' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'title_url' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_update' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'order_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'post_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'last_user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_post' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_view' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'cache_name' => [
                'type' => 'varchar',
                'type_value' => '250',
                'other' => 'DEFAULT NULL',
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'forum_id' => ['forum_id', 'group_id', 'view_id'],
            'group_id' => ['group_id', 'view_id', 'title_url'],
            'forum_id_2' => ['forum_id'],
            'group_id_2' => ['group_id', 'view_id', 'is_announcement'],
            'group_id_3' => ['group_id', 'title_url'],
            'view_id' => ['view_id'],
            'thread_id' => ['thread_id', 'group_id'],
            'start_id' => ['start_id'],
            'view_id_2' => ['view_id', 'title'],
        ];
    }
}