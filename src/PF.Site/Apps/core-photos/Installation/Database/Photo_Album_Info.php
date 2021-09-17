<?php
namespace Apps\Core_Photos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Photo_Album_Info extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'photo_album_info';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'album_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL UNIQUE',
            ],
            'description' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
        ];
    }

}