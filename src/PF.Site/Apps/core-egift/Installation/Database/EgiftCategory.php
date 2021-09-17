<?php

namespace Apps\Core_eGifts\Installation\Database;

use \Core\App\Install\Database\Table;
use Core\App\Install\Database\Field;

class EgiftCategory extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'egift_category';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'category_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'phrase' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255,
                'other' => 'NOT NULL DEFAULT \'\''
            ],
            'time_start' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'time_end' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
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
        $this->_key = [];
    }
}
