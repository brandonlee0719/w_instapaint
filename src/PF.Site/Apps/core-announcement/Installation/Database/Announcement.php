<?php

namespace Apps\Core_Announcement\Installation\Database;

use \Core\App\Install\Database\Table;
use Core\App\Install\Database\Field;

class Announcement extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'announcement';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'announcement_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'subject_var' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'intro_var' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'content_var' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'is_active' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'can_be_closed' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT \'1\''
            ],
            'show_in_dashboard' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'start_date' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'location' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 2,
                'other' => 'NOT NULL DEFAULT \'6\''
            ],
            'country_iso' => [
                'type' => Field::TYPE_CHAR,
                'type_value' => 2,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'gender' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'age_from' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 2,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'age_to' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 2,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'user_group' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255,
                'other' => 'NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'gmt_offset' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 15
            ],
            'style' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 32
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'is_active' => ['is_active', 'show_in_dashboard'],
            'is_active_2' => ['is_active']
        ];
    }
}
