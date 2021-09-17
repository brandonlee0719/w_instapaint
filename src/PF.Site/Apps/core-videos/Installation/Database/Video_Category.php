<?php

namespace Apps\PHPfox_Videos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Video_Category extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'video_category';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'category_id' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'name' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'int',
                'type_value' => '11',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'1\'',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'parent_id' => ['parent_id', 'is_active'],
            'is_active' => ['is_active']
        ];
    }
}
