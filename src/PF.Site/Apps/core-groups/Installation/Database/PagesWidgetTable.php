<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesWidgetTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_widget';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'widget_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'page_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'title' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 200,
                'other' => 'NOT NULL'
            ],
            'is_block' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'menu_title' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 75
            ],
            'url_title' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'image_path' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 75
            ],
            'image_server_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 3,
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
