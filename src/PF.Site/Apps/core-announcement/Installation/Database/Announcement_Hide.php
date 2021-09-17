<?php

namespace Apps\Core_Announcement\Installation\Database;

use \Core\App\Install\Database\Table;
use Core\App\Install\Database\Field;


class Announcement_Hide extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'announcement_hide';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'announcement_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'announcement_id' => ['announcement_id', 'user_id']
        ];
    }
}
