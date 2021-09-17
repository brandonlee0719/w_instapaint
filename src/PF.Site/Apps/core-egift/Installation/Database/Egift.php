<?php

namespace Apps\Core_eGifts\Installation\Database;

use \Core\App\Install\Database\Table;
use Core\App\Install\Database\Field;

class Egift extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'egift';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'egift_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'file_path' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255,
            ],
            'server_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 3,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'category_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'NOT NULL DEFAULT \'0\''
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'title' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255,
                'other' => 'NOT NULL DEFAULT \'\''
            ],
            'price' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255,
                'other' => 'NOT NULL DEFAULT \'\''
            ],
            'ordering' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id' => ['user_id']
        ];
    }
}
