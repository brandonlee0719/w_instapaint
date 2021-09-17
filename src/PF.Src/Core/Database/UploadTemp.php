<?php

namespace Core\Database;

use Core\App\Install\Database\Field as Field;
use Core\App\Install\Database\Table as Table;

/**
 * Class UploadTemp
 *
 * @package Core\Database
 */
class UploadTemp extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'upload_temp';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'upload_id'   => [
                Field::FIELD_PARAM_TYPE           => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE     => 11,
                Field::FIELD_PARAM_OTHER          => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY    => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true,
            ],
            'user_id'     => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
            'destination' => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 127,
            ]
            ,
            'type'        => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 2,
            ],
            'time_stamp'  => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE => 11,
            ],
        ];
    }
}