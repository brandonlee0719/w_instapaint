<?php

namespace Apps\Core_Pages\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesSignupTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_signup';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'signup_id' => [
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
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
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
            'page_id' => ['page_id'],
            'page_id_2' => ['page_id', 'user_id']
        ];
    }
}
