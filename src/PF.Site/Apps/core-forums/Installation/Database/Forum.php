<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'forum_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_category' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'name' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'name_url' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'description' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'is_closed' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'thread_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
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
            'total_thread' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'view_id' => ['view_id'],
            'post_id' => ['post_id'],
            'thread_id' => ['thread_id']
        ];
    }
}