<?php

namespace Apps\PHPfox_Groups\Installation\Database;

use Core\App\Install\Database\Field;
use Core\App\Install\Database\Table;

class PagesWidgetTextTable extends Table
{
    /**
     * Set name of this table, can't missing
     */
    protected function setTableName()
    {
        $this->_table_name = 'pages_widget_text';
    }

    /**
     * Set all fields of table
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'widget_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'text' => [
                'type' => Field::TYPE_MEDIUMTEXT
            ],
            'text_parsed' => [
                'type' => Field::TYPE_MEDIUMTEXT
            ]
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'widget_id' => ['widget_id']
        ];
    }
}
