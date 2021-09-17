<?php
namespace Apps\Core_Marketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Marketplace_Invoice
 * @package Apps\Core_Marketplace\Installation\Database
 */
class Marketplace_Invoice extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'marketplace_invoice';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'invoice_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'listing_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'currency_id' => [
                'type' => 'char',
                'type_value' => '3',
                'other' => 'NOT NULL',
            ],
            'price' => [
                'type' => 'decimal',
                'type_value' => '14',
                'other' => 'NOT NULL DEFAULT \'0.00\'',
            ],
            'status' => [
                'type' => 'varchar',
                'type_value' => '20',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'time_stamp_paid' => [
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
            'listing_id' => ['listing_id'],
            'user_id' => ['user_id'],
            'listing_id_2' => ['listing_id', 'status'],
            'listing_id_3' => ['listing_id', 'user_id', 'status'],
        ];
    }
}