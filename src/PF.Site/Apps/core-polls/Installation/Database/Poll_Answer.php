<?php
namespace Apps\Core_Polls\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Poll_Answer extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'poll_answer';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'answer_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'poll_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'answer' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'total_votes' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'tinyint',
                'type_value' => '3',
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
        ];
    }
}