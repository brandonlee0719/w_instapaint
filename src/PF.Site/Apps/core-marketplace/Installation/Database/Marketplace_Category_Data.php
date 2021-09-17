<?php
namespace Apps\Core_Marketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Marketplace_Category_Data
 * @package Apps\Core_Marketplace\Installation\Database
 */
class Marketplace_Category_Data extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'marketplace_category_data';
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
            'listing_id' => ['listing_id'],
        ];
    }
}