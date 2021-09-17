<?php
namespace Apps\Core_Marketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Marketplace_Text
 * @package Apps\Core_Marketplace\Installation\Database
 */
class Marketplace_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'marketplace_text';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'listing_id' => [
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
            'listing_id' => ['listing_id'],
        ];
    }
}