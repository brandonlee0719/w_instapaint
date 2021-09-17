<?php
namespace Apps\Core_Marketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Marketplace_Image
 * @package Apps\Core_Marketplace\Installation\Database
 */
class Marketplace_Image extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'marketplace_image';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'image_id' => [
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
            'image_path' => [
                'type' => 'varchar',
                'type_value' => '50',
                'other' => 'NOT NULL',
            ],
            'server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'tinyint',
                'type_value' => '3',
                'other' => 'NOT NULL DEFAULT \'0\'',
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