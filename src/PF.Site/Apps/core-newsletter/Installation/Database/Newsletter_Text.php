<?php

namespace Apps\Core_Newsletter\Installation\Database;

use \Core\App\Install\Database\Table as Table;
use \Core\App\Install\Database\Field as Field;

class Newsletter_Text extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'newsletter_text';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'newsletter_id' => [
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true,
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 10,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL'
            ],
            'text_html' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_MEDIUMTEXT,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ],
            'text_plain' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_MEDIUMTEXT,
                Field::FIELD_PARAM_OTHER => 'NOT NULL'
            ]
        ];
    }
}
