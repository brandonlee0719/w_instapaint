<?php
namespace Apps\Core_Blogs\Installation\Database;

use Core\App\Install\Database\Field as Field;
use Core\App\Install\Database\Table as Table;

/**
 * Class Blog_Category_Data
 * @package Apps\Core_Blogs\Installation\Database
 */
class Blog_Category_Data extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'blog_category_data';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'blog_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 10,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL'
            ],
            'category_id' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 10,
                Field::FIELD_PARAM_OTHER => 'UNSIGNED NOT NULL'
            ],
        ];
    }

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [
            'blog_id' => ['blog_id'],
            'category_id' => ['category_id'],
        ];
    }
}
