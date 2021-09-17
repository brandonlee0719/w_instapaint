<?php
namespace Apps\Core_Polls\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Poll_Design extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'poll_design';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'poll_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'auto_increment' => true,
                'primary_key' => true,
            ],
            'background' => [
                'type' => 'varchar',
                'type_value' => '6',
                'other' => 'DEFAULT NULL',
            ],
            'percentage' => [
                'type' => 'varchar',
                'type_value' => '6',
                'other' => 'DEFAULT NULL',
            ],
            'border' => [
                'type' => 'varchar',
                'type_value' => '6',
                'other' => 'DEFAULT NULL',
            ],
        ];
    }


    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'background' => ['background', 'percentage', 'border'],
        ];
    }
}