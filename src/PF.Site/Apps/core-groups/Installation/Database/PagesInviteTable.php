<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesInviteTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_invite';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'invite_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'page_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'type_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'visited_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'invited_user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'invited_email' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 100
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'invited_user_id' => ['invited_user_id'],
            'listing_id' => ['page_id'],
            'listing_id_2' => ['page_id', 'invited_user_id'],
            'listing_id_3' => ['page_id', 'visited_id'],
            'listing_id_4' => ['page_id', 'visited_id', 'invited_user_id'],
            'visited_id' => ['visited_id', 'invited_user_id']
        ];
    }
}
