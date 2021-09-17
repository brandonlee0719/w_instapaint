<?php
namespace Apps\Core_Music\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Music_Feed extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'music_feed';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'song_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'feed_table' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL DEFAULT \'feed\'',
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
            'feed_id'     => ['feed_id'],
        ];
    }
}