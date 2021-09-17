<?php

namespace Apps\Core_RSS\Installation\Database;

use \Core\App\Install\Database\Table as Table;

class Rss_Log extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'rss_log';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'log_id' => [
                'type' => 'int',
                'type_value' => '11',
                'other' => 'UNSIGNED NOT NULL',
                'primary_key' => true,
                'auto_increment' => true,
            ],
            'feed_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'id_hash' => [
                'type' => 'char',
                'type_value' => '32',
                'other' => 'NOT NULL',
            ],
            'ip_address' => [
                'type' => 'varchar',
                'type_value' => '15',
                'other' => 'DEFAULT NULL',
            ],
            'user_agent' => [
                'type' => 'varchar',
                'type_value' => '100',
                'other' => 'NOT NULL',
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
            'feed_id' => ['feed_id', 'id_hash'],
        ];
    }
}
