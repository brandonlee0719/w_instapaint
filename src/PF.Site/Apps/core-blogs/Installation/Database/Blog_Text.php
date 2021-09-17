<?php
namespace Apps\Core_Blogs\Installation\Database;

use Core\App\Install\Database\Field as Field;
use Core\App\Install\Database\Table as Table;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Blog_Text
 * @package Apps\Core_Blogs\Installation\Database
 */
class Blog_Text extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'blog_text';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'blog_id' => [
                Field::FIELD_PARAM_PRIMARY_KEY => true,
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 10,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL'
            ],
            'text' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_MEDIUMTEXT,
            ],
            'text_parsed' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_MEDIUMTEXT,
            ],
        ];
    }
}
