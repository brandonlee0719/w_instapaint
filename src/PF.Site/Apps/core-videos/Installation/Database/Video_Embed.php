<?php

namespace Apps\PHPfox_Videos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Video_Embed extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'video_embed';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'video_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'video_url' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'embed_code' => [
                'type' => 'mediumtext',
                'other' => 'NOT NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'video_id' => ['video_id']
        ];
    }
}
