<?php
namespace Apps\Core_Music\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Music_Profile extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'music_profile';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'play_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'song_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_id' => [
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
            'song_id'     => ['song_id'],
            'user_id'     => ['user_id'],
        ];
    }
}