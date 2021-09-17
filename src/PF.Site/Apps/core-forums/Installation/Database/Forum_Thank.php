<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Thank
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Thank extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_thank';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'thank_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'post_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'user_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
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
            'post_id' => ['post_id', 'user_id'],
        ];
    }
}