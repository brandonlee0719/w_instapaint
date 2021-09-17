<?php
namespace Apps\Core_Events\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Event_Feed_Comment
 * @package Apps\Core_Events\Installation\Database
 */
class Event_Feed_Comment extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'event_feed_comment';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_comment_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
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
            'privacy' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'privacy_comment' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'content' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'total_comment' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_like' => [
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
        ];
    }
}