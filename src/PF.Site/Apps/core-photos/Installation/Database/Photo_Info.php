<?php
namespace Apps\Core_Photos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Photo_Info extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'photo_info';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'photo_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL UNIQUE',
            ],
            'file_name' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'NOT NULL',
            ],
            'file_size' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'mime_type' => [
                'type' => 'varchar',
                'type_value' => '150',
                'other' => 'DEFAULT NULL',
            ],
            'extension' => [
                'type' => 'varchar',
                'type_value' => '20',
                'other' => 'NOT NULL',
            ],
            'description' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'width' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'height' => [
                'type' => 'smallint',
                'type_value' => '4',
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

}