<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesFeedCommentTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_feed_comment';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_comment_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'parent_user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'privacy' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 3,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'privacy_comment' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 3,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'content' => [
                'type' => Field::TYPE_TEXT
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'total_comment' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'total_like' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'parent_user_id' => ['parent_user_id']
        ];
    }
}
