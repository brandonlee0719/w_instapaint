<?php
namespace Apps\Core_Quizzes\Installation\Database;

use Core\App\Install\Database\Table as Table;

class Quiz_Answer extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'quiz_answer';
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
            'question_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'answer' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'is_correct' => [
                'type' => 'tinyint',
                'type_value' => '1',
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
            'question_id' => ['question_id'],
        ];
    }
}