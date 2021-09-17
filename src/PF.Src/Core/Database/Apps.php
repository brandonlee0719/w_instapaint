<?php

namespace Core\Database;

use Core\App\Install\Database\Field as Field;
use Core\App\Install\Database\Table as Table;

/**
 * Class Apps
 *
 * @package Core\Database
 */
class Apps extends Table
{
    /**
     *
     */
    protected function setTableName()
    {
        $this->_table_name = 'apps';
    }

    /**
     *
     */
    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'apps_key'    => [
                Field::FIELD_PARAM_TYPE           => Field::TYPE_INT,
                Field::FIELD_PARAM_TYPE_VALUE     => 11,
                Field::FIELD_PARAM_OTHER          => 'UNSIGNED NOT NULL',
                Field::FIELD_PARAM_PRIMARY_KEY    => true,
                Field::FIELD_PARAM_AUTO_INCREMENT => true,
            ],
            'apps_id'     => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'apps_dir'    => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 100,
            ]
            ,
            'apps_name'   => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'version'     => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 32,
                Field::FIELD_PARAM_OTHER      => 'DEFAULT \'4.5.0\'',
            ],
            'apps_alias'  => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'author'      => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'vendor'      => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            'description' => [
                Field::FIELD_PARAM_TYPE => Field::TYPE_TEXT,
            ],
            'apps_icon'   => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_VARCHAR,
                Field::FIELD_PARAM_TYPE_VALUE => 255,
            ],
            /**
             * type = 1 mean core
             * Type = 2 mean 3rd-party
             */
            'type'        => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
            ],
            'is_active'   => [
                Field::FIELD_PARAM_TYPE       => Field::TYPE_TINYINT,
                Field::FIELD_PARAM_TYPE_VALUE => 1,
            ],
        ];
    }
}