<?php
namespace Apps\Core_Music\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Music_Album_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'music_album_text';
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
                'other' => 'UNSIGNED NOT NULL',
            ],
            'text' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'text_parsed' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'album_id'     => ['album_id'],
        ];
    }
}