<?php
namespace Apps\Core_Events\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Event_Text
 * @package Apps\Core_Events\Installation\Database
 */
class Event_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'event_text';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'event_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'description' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'description_parsed' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],

        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'event_id' => ['event_id'],
        ];
    }
}