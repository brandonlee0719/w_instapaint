<?php
namespace Apps\PHPfox_Core\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Currency extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'currency';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'currency_id' => [
                'type' => 'varchar',
                'type_value' => '3',
                'other' => 'NOT NULL',
            ],
            'symbol' => [
                'type' => 'varchar',
                'type_value' => '15',
                'other' => 'NOT NULL',
            ],
            'phrase_var' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'ordering' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'format' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'NOT NULL DEFAULT \'{0} #,###.00 {1}\'',
            ],
            'is_default' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
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
            'currency_id'     => ['currency_id'],
            'is_active'     => ['is_active'],
        ];
    }
}