<?php

namespace Apps\PHPfox_Videos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Video extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'video';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'video_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'in_process' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_stream' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_featured' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_spotlight' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_sponsor' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'DEFAULT NULL',
            ],
            'item_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'privacy' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'privacy_comment' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'parent_user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'destination' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'file_ext' => [
                'type' => 'varchar',
                'type_value' => '10',
                'other' => 'DEFAULT NULL',
            ],
            'duration' => [
                'type' => 'varchar',
                'type_value' => '8',
                'other' => 'DEFAULT NULL',
            ],
            'resolution_x' => [
                'type' => 'varchar',
                'type_value' => '4',
                'other' => 'DEFAULT NULL',
            ],
            'resolution_y' => [
                'type' => 'varchar',
                'type_value' => '4',
                'other' => 'DEFAULT NULL',
            ],
            'image_path' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'image_server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'total_comment' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_like' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_dislike' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_score' => [
                'type' => 'decimal',
                'type_value' => '4',
                'other' => 'NOT NULL DEFAULT \'0.00\'',
            ],
            'total_rating' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_view' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'status_info' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'page_user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'location_latlng' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'DEFAULT NULL',
            ],
            'location_name' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'in_process' => ['in_process'],
            'user_id' => ['user_id'],
            'view_id' => ['view_id'],
            'in_process_2' => ['in_process', 'view_id', 'item_id', 'privacy'],
            'in_process_3' => ['in_process', 'view_id', 'item_id', 'user_id'],
            'in_process_4' => ['in_process', 'view_id', 'item_id', 'privacy', 'title'],
            'in_process_5' => ['in_process', 'view_id', 'item_id', 'privacy', 'user_id'],
            'in_process_6' => ['in_process', 'view_id', 'privacy', 'title'],
        ];
    }
}
