<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesCategoryTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_category';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'category_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_MEDIUMINT,
                'type_value' => 8,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'type_id' => [
                'type' => Field::TYPE_SMALLINT,
                'type_value' => 4,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'name' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 200,
                'other' => 'NOT NULL'
            ],
            'page_type' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'is_active' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 1'
            ],
            'ordering' => [
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
            'category_id' => ['category_id', 'is_active'],
            'type_id' => ['type_id', 'is_active']
        ];
    }
}
