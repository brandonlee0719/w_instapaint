<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesPermTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_perm';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'page_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'var_name' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 150,
                'other' => 'NOT NULL'
            ],
            'var_value' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'page_id' => ['page_id']
        ];
    }
}
