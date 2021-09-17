<?php

namespace Apps\Core_RSS\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Rss_Group extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'rss_group';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'group_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL',
            ],
            'product_id' => [
                'type' => 'varchar',
                'type_value' => '25',
                'other' => 'NOT NULL',
            ],
            'name_var' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'smallint',
                'type_value' => '4',
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
            'is_active' => ['is_active'],
        ];
    }
}
