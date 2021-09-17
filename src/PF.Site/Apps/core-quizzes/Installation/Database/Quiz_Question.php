<?php
namespace Apps\Core_Quizzes\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Quiz_Question extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'quiz_question';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'question_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'auto_increment' => true,
                'primary_key' => true,
            ],
            'quiz_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'question' => [
                'type' => 'varchar',
                'type_value' => '255',
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
            'quiz_id' => ['quiz_id'],
        ];
    }
}