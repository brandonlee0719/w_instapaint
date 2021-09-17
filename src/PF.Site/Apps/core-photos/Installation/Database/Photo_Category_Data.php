<?php
namespace Apps\Core_Photos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Photo_Category_Data extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'photo_category_data';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'photo_id' => [
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
            'photo_id' => ['photo_id'],
            'category_id' => ['category_id']
        ];
    }
}