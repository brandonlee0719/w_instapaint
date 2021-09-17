<?php
namespace Apps\phpFox_Shoutbox\Installation\Database;

use \Core\App\Install\Database\Field as Field;
use \Core\App\Install\Database\Table as Table;

/**
 * Class Shoutbox
 * @author  Neil <neil@phpfox.com>
 * @package Apps\phpFox_Shoutbox\Installation
 */
class Shoutbox extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'shoutbox';
    }
    
    public function getTableName()
    {
        return $this->_table_name;
    }
    
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'shoutbox_id'      => [
                Field::FIELD_PARAM_TYPE           => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE     => 11,
                Field::FIELD_PARAM_OTHER          => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY    => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true
            ],
            'parent_module_id' => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'parent_item_id'   => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'user_id'          => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11
            ],
            'text'             => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 1023
            ],
            'timestamp'        => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11
            ]
        ];
    }
}