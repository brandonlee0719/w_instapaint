<?php

namespace Apps\Core_Pages\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesTypeTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_type';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'type_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_SMALLINT,
                'type_value' => 4,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'is_active' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 1'
            ],
            'item_type' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'name' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 200,
                'other' => 'NOT NULL'
            ],
            'image_path' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 200
            ],
            'image_server_id' => [
                'type' => Field::TYPE_SMALLINT,
                'type_value' => 200
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'ordering' => [
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
            'is_active' => ['is_active']
        ];
    }
}
