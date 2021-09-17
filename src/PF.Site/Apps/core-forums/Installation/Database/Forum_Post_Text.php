<?php
namespace Apps\Core_Forums\Installation\Database;

use Core\App\Install\Database\Table as Table;

/**
 * Class Forum_Post_Text
 * @package Apps\Core_Forums\Installation\Database
 */
class Forum_Post_Text extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'forum_post_text';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'post_id' => [
                'type' => 'int',
                'type_value' => '10',
                'other' => 'UNSIGNED NOT NULL',
            ],
            'text' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
            'text_parsed' => [
                'type' => 'mediumtext',
                'other' => 'NULL',
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'post_id' => ['post_id'],
        ];
    }
}