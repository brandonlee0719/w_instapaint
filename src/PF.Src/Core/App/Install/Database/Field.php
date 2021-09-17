<?php

namespace Core\App\Install\Database;

/**
 * Class Structure Make a database table structure
 *
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.5.0
 * @package Core\App\Install\Database
 */
class Field
{
    //Int type
    const TYPE_INT       = 'int';
    const TYPE_BIGINT    = 'bigint';
    const TYPE_MEDIUMINT = 'mediumint';
    const TYPE_SMALLINT  = 'smallint';
    const TYPE_TINYINT   = 'tinyint';

    //float type
    const TYPE_DECIMAL = 'decimal';
    const TYPE_FLOAT   = 'float';
    const TYPE_DOUBLE  = 'double';
    const TYPE_REAL    = 'real';

    //boolean type
    const TYPE_BIT     = 'bit';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_SERIAL  = 'serial';

    //datetime
    const TYPE_DATE      = 'date';
    const TYPE_DATETIME  = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_TIME      = 'time';
    const TYPE_YEAR      = 'year';

    //char
    const TYPE_CHAR    = 'char';
    const TYPE_VARCHAR = 'varchar';

    //text
    const TYPE_TEXT       = 'text';
    const TYPE_TINYTEXT   = 'tinytext';
    const TYPE_MEDIUMTEXT = 'mediumtext';
    const TYPE_LONGTEXT   = 'longtext';

    //binary
    const TYPE_BINARY    = 'binary';
    const TYPE_VARBINARY = 'varbinary';

    //int
    const TYPE_ENUM = 'enum';
    const TYPE_SET  = 'set';


    //other
    const TYPE_GEOMETRY           = 'geometry';
    const TYPE_POINT              = 'point';
    const TYPE_LINESTRING         = 'linestring';
    const TYPE_POLYGON            = 'polygon';
    const TYPE_MULTIPOINT         = 'multipoint';
    const TYPE_MULTILINESTRING    = 'multilinestring';
    const TYPE_MULTIPOLYGON       = 'multipolygon';
    const TYPE_GEOMETRYCOLLECTION = 'geometrycollection';

    const TYPE_BLOB       = 'blob';
    const TYPE_TINYBLOB   = 'tinyblob';
    const TYPE_MEDIUMBLOB = 'mediumblob';
    const TYPE_LONGBLOB   = 'longblob';

    //Field param keys
    const FIELD_PARAM_TYPE           = 'type';
    const FIELD_PARAM_TYPE_VALUE     = 'type_value';
    const FIELD_PARAM_OTHER          = 'other';
    const FIELD_PARAM_PRIMARY_KEY    = 'primary_key';
    const FIELD_PARAM_AUTO_INCREMENT = 'auto_increment';

    // map type with general type
    private $_aMap
        = [
            'int'       => [
                'custom'  => 'INT:',
                'default' => 'INT:11',
            ],
            'bigint'    => 'BINT',
            'mediumint' => [
                'custom'  => 'INT:',
                'default' => 'INT:8',
            ],
            'smallint'  => [
                'custom'  => 'INT:',
                'default' => 'INT:4',
            ],
            'tinyint'   => [
                'custom'  => 'TINT:',
                'default' => 'TINT:1',
            ],

            'decimal' => [
                'custom'  => 'DECIMAL:',
                'default' => 'DECIMAL',
            ],
            'float'   => 'MDECIMAL:',
            'double'  => 'MDECIMAL:',
            'real'    => 'MDECIMAL:',

            'bit'     => 'TEXT',
            'boolean' => 'BOOL',
            'serial'  => 'TEXT',

            'date'      => 'VCHAR',
            'datetime'  => 'VCHAR',
            'timestamp' => 'TIMESTAMP',
            'time'      => 'VCHAR',
            'year'      => 'VCHAR',

            'char'    => [
                'custom'  => 'CHAR:',
                'default' => 'CHAR:255',
            ],
            'varchar' => [
                'custom'  => 'VCHAR:',
                'default' => 'VCHAR',
            ],

            'text'       => 'TEXT',
            'tinytext'   => 'XSTEXT',
            'mediumtext' => 'MTEXT',
            'longtext'   => 'MTEXT',

            'binary'    => 'BINARY',
            'varbinary' => 'VARBINARY',

            'enum' => 'ENUM',
            'set'  => 'SET',

            'geometry'           => 'GEOMETRY',
            'point'              => 'POINT',
            'linestring'         => 'LINESTRING',
            'polygon'            => 'POLYGON',
            'multipoint'         => 'MULTIPOINT',
            'multilinestring'    => 'MULTILINESTRING',
            'multipolygon'       => 'MULTIPOLYGON',
            'geometrycollection' => 'GEOMETRYCOLLECTION',

            'blob'       => 'BLOB',
            'tinyblob'   => 'TINYBLOB',
            'mediumblob' => 'MEDIUMBLOB',
            'longblob'   => 'LONGBLOB',
        ];

    /**
     * @var string is Name of a files
     */
    private $_name = '';

    /**
     * @var string is type of this field. the value only accepted by TYPE_* const
     */
    private $_type;

    /**
     * @var int is the length of $_type. Only available for some type
     */
    private $_type_value = 0;

    /**
     * @var null|bool
     */
    private $_null = null;

    /**
     * @var null|string
     */
    private $_default = null;

    /**
     * @var string is Other property of this value. For simple, we put all of them in one variable
     */
    private $_other = '';

    /**
     * Field constructor.
     *
     * @param array $aParam
     */
    public function __construct($aParam = [])
    {
        if (isset($aParam['name'])) {
            $this->_name = $aParam['name'];
        }

        if (isset($aParam['type'])) {
            $this->_type = $aParam['type'];
        }

        if (isset($aParam['type_value'])) {
            $this->_type_value = $aParam['type_value'];
        }

        if (isset($aParam['null'])) {
            $this->_null = $aParam['null'];
        }

        if (isset($aParam['default'])) {
            $this->_default = $aParam['default'];
        }

        if (isset($aParam['other'])) {
            $this->_other = $aParam['other'];
        }

    }

    /**
     * Set name of this field
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get name of this field
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set type of this field
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Get Type of this field
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get full type of this field
     *
     * @return string
     */
    public function getFullType()
    {
        $type = $this->_aMap[$this->_type];
        $sCode = is_array($type)
            ? ($this->_type_value > 0 ? $type['custom'] . $this->_type_value : $type['default'])
            :
            ($this->_type_value > 0 ? $type . $this->_type_value : $type);

        return $sCode;
    }

    /**
     * @return bool|null
     */
    public function getNull()
    {
        return $this->_null;
    }

    /**
     * @param bool|null $null
     */
    public function setNull($null)
    {
        $this->_null = $null;
    }

    /**
     * @return null|string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * @param null|string $default
     */
    public function setDefault($default)
    {
        $this->_default = $default;
    }

    /**
     * Get other property of this field
     *
     * @return string
     */
    public function getOther()
    {
        return $this->_other;
    }

    /**
     * Set other property of this field
     *
     * @param string $other
     */
    public function setOther($other)
    {
        $this->_other = $other;
    }

    /**
     * Set value of Type of this field
     *
     * @param int $type_value
     */
    public function setTypeValue($type_value)
    {
        $this->_type_value = $type_value;
    }

    /**
     * Get value of Type of this field
     *
     * @return int
     */
    public function getTypeValue()
    {
        return $this->_type_value;
    }

    /**
     * Get all valid type value
     *
     * @return array
     */
    private function getValidType()
    {
        $type = [
            self::TYPE_INT,
            self::TYPE_BIGINT,
            self::TYPE_MEDIUMINT,
            self::TYPE_SMALLINT,
            self::TYPE_TINYINT,
            self::TYPE_DECIMAL,
            self::TYPE_FLOAT,
            self::TYPE_DOUBLE,
            self::TYPE_REAL,
            self::TYPE_BIT,
            self::TYPE_BOOLEAN,
            self::TYPE_SERIAL,
            self::TYPE_DATE,
            self::TYPE_DATETIME,
            self::TYPE_TIMESTAMP,
            self::TYPE_TIME,
            self::TYPE_YEAR,
            self::TYPE_CHAR,
            self::TYPE_VARCHAR,
            self::TYPE_TEXT,
            self::TYPE_TINYTEXT,
            self::TYPE_MEDIUMTEXT,
            self::TYPE_LONGTEXT,
            self::TYPE_BINARY,
            self::TYPE_VARBINARY,
            self::TYPE_ENUM,
            self::TYPE_SET,
            self::TYPE_GEOMETRY,
            self::TYPE_POINT,
            self::TYPE_LINESTRING,
            self::TYPE_POLYGON,
            self::TYPE_MULTIPOINT,
            self::TYPE_MULTILINESTRING,
            self::TYPE_MULTIPOLYGON,
            self::TYPE_GEOMETRYCOLLECTION,
            self::TYPE_BLOB,
            self::TYPE_TINYBLOB,
            self::TYPE_MEDIUMBLOB,
            self::TYPE_LONGBLOB,
        ];
        return $type;
    }

    /**
     * Check a Name is valid
     *
     * @return bool
     */
    private function isValidName()
    {
        if (empty($this->_name)) {
            return false;
        }
        //todo should have more check about name
        return true;
    }

    /**
     * Check a type is valid
     *
     * @return bool
     */
    private function isValidType()
    {
        if (empty($this->_type)) {
            return false;
        }
        $aType = $this->getValidType();
        if (in_array($this->_type, $aType)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check is this field valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->isValidName()) {
            return false;
        }

        if (!$this->isValidType()) {
            return false;
        }
        return true;
    }
}
