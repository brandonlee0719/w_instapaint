<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Post
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Post extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_post';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'post_id' => [
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
            'view_id' => [
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
            'total_attachment' => [
                'type' => 'int',
                'type_value' => '11',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'update_time' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'update_user' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'DEFAULT NULL',
            ],
            'total_like' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_dislike' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'cache_name' => [
                'type' => 'varchar',
                'type_value' => '250',
                'other' => 'DEFAULT NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'thread_id' => ['thread_id'],
            'user_id' => ['user_id'],
            'view_id' => ['view_id'],
        ];
    }
}