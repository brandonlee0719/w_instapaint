<?php
namespace Apps\PHPfox_Core\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Tag extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'tag';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'tag_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'item_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'category_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'tag_text' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'tag_url' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'tag_type' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'1\'',
            ],
            'added' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'NOT NULL',
            ],
        ];
    }
    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'user_id'     => ['user_id','tag_text'],
            'item_id'     => ['item_id','category_id'],
            'category_id'     => ['category_id'],
            'tag_url'     => ['tag_url'],
            'user_search'     => ['category_id','user_id','tag_text'],
            'user_search_general'     => ['category_id','user_id'],
            'item_id_2'     => ['item_id','category_id','user_id'],
            'item_id_3'     => ['item_id','category_id','tag_url'],
            'category_id_2'     => ['category_id','tag_text'],
        ];
    }
}