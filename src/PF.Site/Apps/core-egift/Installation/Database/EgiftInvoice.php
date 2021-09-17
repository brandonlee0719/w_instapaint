<?php

namespace Apps\Core_eGifts\Installation\Database;

use \Core\App\Install\Database\Table;
use Core\App\Install\Database\Field;

class EgiftInvoice extends Table
{
    protected function setTableName()
    {
        $this->_table_name = 'egift_invoice';
    }

    protected function setFieldParams()
    {
        $this->_aFieldParams = [
            'invoice_id' => [
                'primary_key' => true,
                'auto_increment' => true,
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL'
            ],
            'user_from' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'user_to' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'egift_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'birthday_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'feed_id' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED'
            ],
            'currency_id' => [
                'type' => Field::TYPE_CHAR,
                'type_value' => 3,
                'other' => 'NOT NULL DEFAULT \'\''
            ],
            'price' => [
                'type' => Field::TYPE_DECIMAL,
                'type_value' => '14,2',
                'other' => 'NOT NULL DEFAULT \'0.00\''
            ],
            'time_stamp_created' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'time_stamp_paid' => [
                'type' => Field::TYPE_INT,
                'type_value' => 11,
                'other' => 'UNSIGNED NOT NULL DEFAULT \'0\''
            ],
            'status' => [
                'type' => Field::TYPE_VARCHAR,
                'type_value' => 20,
                'other' => 'NOT NULL DEFAULT \'pending\''
            ]
        ];
    }
}
