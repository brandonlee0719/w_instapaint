<?php

namespace Apps\PHPfox_Videos\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Video_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'video_text';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'video_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL UNIQUE',
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
}
