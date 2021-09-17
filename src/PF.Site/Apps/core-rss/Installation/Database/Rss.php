<?php

namespace Apps\Core_RSS\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Rss extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'rss';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'feed_id' => [
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
            'group_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'title_var' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'description_var' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'feed_link' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'php_group_code' => [
                'type' => 'mediumtext',
                'other' => 'NULL'
            ],
            'php_view_code' => [
                'type' => 'mediumtext',
            ],
            'is_active' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_site_wide' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'total_subscribed' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'ordering' => [
                'type' => 'mediumint',
                'type_value' => '8',
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
            'group_id' => ['group_id', 'is_active'],
            'feed_id' => ['feed_id', 'is_active'],
            'is_active' => ['is_active', 'is_site_wide'],
        ];
    }
}
