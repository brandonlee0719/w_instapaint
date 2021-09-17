<?php
namespace Apps\Core_Music\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Music_Song extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'music_song';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'song_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
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
            'is_featured' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_sponsor' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'album_id' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'genre_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'description' => [
                'type' => 'text',
                'other' => 'NULL',
            ],
            'description_parsed' => [
                'type' => 'text',
                'other' => 'NULL',
            ],
            'song_path' => [
                'type' => 'varchar',
                'type_value' => '50',
                'other' => 'DEFAULT NULL',
            ],
            'server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'explicit' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL',
            ],
            'duration' => [
                'type' => 'varchar',
                'type_value' => '5',
                'other' => 'DEFAULT NULL',
            ],
            'ordering' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'image_path' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'DEFAULT NULL',
            ],
            'image_server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'total_play' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_view' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
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
            'total_attachment' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
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
        ];
    }
    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id'     => ['user_id'],
            'view_id'     => ['view_id','privacy','genre_id'],
            'view_id_2'   => ['view_id', 'privacy','is_featured'],
            'view_id_3'     => ['view_id','privacy'],
            'view_id_4'       => ['view_id', 'privacy','user_id'],
            'view_id_5'       => ['view_id', 'privacy','title'],
            'view_id_6'       => ['view_id', 'privacy','module_id','item_id'],
            'view_id_7'       => ['view_id', 'privacy','item_id'],
        ];
    }
}