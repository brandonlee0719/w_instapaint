<?php
namespace Apps\Core_Polls\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Poll_Result extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'poll_result';
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
            ],
            'answer_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp' => [
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
            'poll_id' => ['poll_id'],
            'answer_id' => ['answer_id'],
            'user_id' => ['user_id'],
            'item_id' => ['poll_id', 'answer_id'],
        ];
    }
}