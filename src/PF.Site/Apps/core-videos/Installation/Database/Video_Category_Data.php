<?php

namespace Apps\PHPfox_Videos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Video_Category_Data extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'video_category_data';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'video_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'category_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'category_id' => ['category_id'],
            'video_id' => ['video_id']
        ];
    }
}
