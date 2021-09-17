<?php

namespace Apps\Core_Poke\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class Poke_Data extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'poke_data';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'poke_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'to_user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'status_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT \'1\''
            ],
            'total_like' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'total_comment' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'NOT NULL DEFAULT \'0\''
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'to_user_id' => ['to_user_id'],
            'user_id' => ['user_id', 'to_user_id'],
            'user_id_2' => ['user_id', 'to_user_id', 'status_id']
        ];
    }
}
