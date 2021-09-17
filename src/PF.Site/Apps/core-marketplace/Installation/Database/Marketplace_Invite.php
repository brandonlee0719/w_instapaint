<?php
namespace Apps\Core_Marketplace\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Marketplace_Invite
 * @package Apps\Core_Marketplace\Installation\Database
 */
class Marketplace_Invite extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'marketplace_invite';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'invite_id' => [
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
            'type_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'visited_id' => [
                'type' => 'tinyint',
                'type_value' => '1',
                'other' => 'NOT NULL DEFAULT \'0\'',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'invited_user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\'',
            ],
            'invited_email' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'DEFAULT NULL',
            ],
            'time_stamp' => [
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
            'listing_id' => ['listing_id'],
            'listing_id_2' => ['listing_id', 'invited_user_id'],
            'invite_user_id' => ['invited_user_id'],
            'listing_id_3' => ['listing_id', 'visited_id'],
            'listing_id_4' => ['listing_id', 'visited_id', 'invited_user_id'],
            'visited_id' => ['visited_id', 'invited_user_id'],
        ];
    }
}