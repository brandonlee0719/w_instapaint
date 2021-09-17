<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Moderator_Access
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Moderator_Access extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_moderator_access';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'moderator_id' => [
                'type' => 'smallint',
                'type_value' => '4',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'var_name' => [
                'type' => 'varchar',
                'type_value' => '150',
                'other' => 'NOT NULL',
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'moderator_id' => ['moderator_id'],
        ];
    }
}