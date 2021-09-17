<?php
namespace Apps\Core_Events\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Event
 * @package Apps\Core_Events\Installation\Database
 */
class Event extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'event';
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
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'view_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_featured' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'is_sponsor' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'privacy' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'privacy_comment' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'module_id' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'NOT NULL DEFAULT \'event\'',
            ],
            'item_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'title' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'NOT NULL',
            ],
            'location' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'country_iso' => [
                'type' => 'char',
                'type_value' => '2',
                'other' => 'DEFAULT NULL',
            ],
            'country_child_id' => [
                'type' => 'mediumint',
                'type_value' => '8',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'postal_code' => [
                'type' => 'varchar',
                'type_value' => '20',
                'other' => 'DEFAULT NULL',
            ],
            'city' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'start_time' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'end_time' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'image_path' => [
                'type' => 'varchar',
                'type_value' => '75',
                'other' => 'DEFAULT NULL',
            ],
            'server_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'total_comment' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_like' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_dislike' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_view' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'total_attachment' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'mass_email' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'start_gmt_offset' => [
                'type' => 'varchar',
                'type_value' => '15',
                'other' => 'DEFAULT NULL'
            ],
            'end_gmt_offset' => [
                'type' => 'varchar',
                'type_value' => '15',
                'other' => 'DEFAULT NULL'
            ],
            'gmap' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL'
            ],
            'address' => [
                'type' => 'varchar',
                'type_value' => '255',
                'other' => 'DEFAULT NULL'
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'module_id' => ['module_id', 'item_id'],
            'user_id' => ['user_id'],
            'view_id' => ['view_id', 'privacy', 'item_id', 'start_time'],
            'view_id_2' => ['view_id', 'privacy', 'item_id', 'user_id', 'start_time'],
            'view_id_5' => ['view_id', 'privacy', 'module_id', 'item_id', 'start_time'],
            'start_time' => ['start_time', 'view_id']
        ];
    }
}