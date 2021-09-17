<?php
namespace Apps\Core_Events\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Event_Feed
 * @package Apps\Core_Events\Installation\Database
 */
class Event_Feed extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'event_feed';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'privacy' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'privacy_comment' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'type_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'parent_user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'item_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'parent_feed_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'parent_module_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'DEFAULT NULL',
            ],
            'time_update' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'parent_user_id' => ['parent_user_id'],
            'time_update' => ['time_update'],
        ];
    }
}