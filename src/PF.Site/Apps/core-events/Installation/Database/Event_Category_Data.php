<?php
namespace Apps\Core_Events\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Event_Category_Data
 * @package Apps\Core_Events\Installation\Database
 */
class Event_Category_Data extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'event_category_data';
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
            'category_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'category_id' => ['category_id'],
            'event_id' => ['event_id'],
        ];
    }
}