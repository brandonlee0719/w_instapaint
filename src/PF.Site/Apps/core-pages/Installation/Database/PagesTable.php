<?php

namespace Apps\Core_Pages\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'page_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'app_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'view_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'type_id' => [
                'type' => Field::TYPE_SMALLINT,
                'type_value' => 4,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'category_id' => [
                'type' => Field::TYPE_MEDIUMINT,
                'type_value' => 8,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'title' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'reg_method' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'landing_page' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 50
            ],
            'time_stamp' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED'
            ],
            'image_path' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 75
            ],
            'image_server_id' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 3,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'total_like' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'total_dislike' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'total_comment' => [
                'type' => Field::TYPE_INT,
                'type_value' => 10,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'privacy' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'designer_style_id' => [
                'type' => Field::TYPE_SMALLINT,
                'type_value' => 4,
                'other' => 'UNSIGNED NOT NULL DEFAULT 0'
            ],
            'cover_photo_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED'
            ],
            'cover_photo_position' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 5
            ],
            'location_latitude' => [
                'type' => Field::TYPE_DECIMAL,
                'type_value' => '30, 27'
            ],
            'location_longitude' => [
                'type' => Field::TYPE_DECIMAL,
                'type_value' => '30, 27'
            ],
            'location_name' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 255
            ],
            'use_timeline' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
                'other' => 'NOT NULL DEFAULT 0'
            ],
            'item_type' => [
                'type' => Field::TYPE_TINYINT,
                'type_value' => 1,
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
            'app_id' => ['app_id'],
            'app_id_2' => ['app_id', 'view_id', 'privacy'],
            'app_id_3' => ['app_id', 'view_id', 'user_id'],
            'app_id_4' => ['app_id', 'view_id'],
            'category_id' => ['category_id'],
            'latitude' => ['location_latitude', 'location_longitude'],
            'page_id' => ['page_id', 'view_id'],
            'type_id' => ['type_id', 'time_stamp'],
            'view_id' => ['view_id', 'title', 'privacy'],
            'view_id_2' => ['view_id']
        ];
    }
}
