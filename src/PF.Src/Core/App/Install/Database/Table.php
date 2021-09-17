<?php

namespace Core\App\Install\Database;

use Phpfox;

/**
 * Class Table
 *
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Core\App\Install\Database
 */
abstract class Table
{
    /**
     * @var string name of the table
     */
    protected $_table_name;

    /**
     * @var string engine of table
     */
    protected $_engine = 'InnoDB';

    /**
     * @var string charset of table
     */
    protected $_charset = 'latin1';

    /**
     * @var Field is a Field of auto increment
     *
     */
    private $_auto_increment = '';

    /**
     * @var array of primary keys
     */
    private $_primary_key = [];

    /**
     * @var array of keys
     */
    protected $_key = [];

    /**
     * @var array structure of table in array
     */
    protected $_aFieldParams = [];

    /**
     * @var array of Field, store all fields of this table
     */
    protected $_aFields = [];

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->setTableName();
        $this->setFieldParams();
        $this->_table_name = Phpfox::getT($this->_table_name);
        foreach ($this->_aFieldParams as $key => $aParam) {
            $aParam['name'] = $key;
            $newField = new Field($aParam);
            if (!$newField->isValid()) {
                continue;
            }
            $this->addField($newField);
            if (isset($aParam['primary_key'])) {
                $bAuto = (isset($aParam['auto_increment'])) ? $aParam['auto_increment'] : false;
                $this->addPrimaryKey($newField, $bAuto);
            }
        }
        $this->setKeys();
    }

    /**
     * Set name of this table, can't missing
     */
    abstract protected function setTableName();

    /**
     * Set all fields of table
     */
    abstract protected function setFieldParams();

    /**
     * Set keys of table
     */
    protected function setKeys()
    {
        $this->_key = [];
    }

    /**
     * Add a new field for this table
     *
     * @param Field $field
     *
     * @return bool
     */
    private function addField($field)
    {
        if ($field->isValid()) {
            $this->_aFields[] = $field;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a primary and auto increment for this table
     *
     * @param Field $field
     * @param bool  $bAutoIncrement
     */
    private function addPrimaryKey($field, $bAutoIncrement = true)
    {
        $this->_primary_key[] = $field->getName();
        if ($bAutoIncrement) {
            $this->_auto_increment = $field;
        }
    }

    /**
     * Add a key for this table
     *
     * @param string $name
     * @param array  $listKey of Field
     *
     * @return bool
     */
    protected function addKey($name, $listKey)
    {
        if (count($listKey) == 0) {
            return false;
        }
        $aKey = [];
        foreach ($listKey as $key) {
            if (!is_a($key, '\Apps\PHPfox_Backup_Restore\Install\Database\Field')) {
                continue;
            }
            $aKey[] = $key->getName();
        }
        if (count($aKey)) {
            $this->_key[$name] = $aKey;
        } else {
            //Don't have any valid key
            return false;
        }
        return true;
    }

    /**
     * Create table
     */
    public function createTable()
    {
        if (count($this->_aFields) == 0) {
            return;
        }

        $aFields = [];
        /** @var $field Field * */
        foreach ($this->_aFields as $field) {
            if (!is_a($field, '\Core\App\Install\Database\Field') || !$field->isValid()) {
                return;
            }
            $aFields[] = [
                'name'           => $field->getName(),
                'type'           => $field->getFullType(),
                'extra'          => $field->getOther(),
                'primary_key'    => in_array($field->getName(), $this->_primary_key),
                'auto_increment' => is_a($this->_auto_increment, '\Core\App\Install\Database\Field') && ($this->_auto_increment->getName() == $field->getName()),
            ];
        }
        Phpfox::getLib('database')->createTable($this->_table_name, $aFields, false, $this->_key);
    }

    /**
     * Truncate this table, use for reset
     */
    public function truncate()
    {
        Phpfox::getLib('database')->truncateTable($this->_table_name);
    }

    /**
     * Drop this table, use for uninstall
     */
    public function drop()
    {
        Phpfox::getLib('database')->dropTable($this->_table_name);
    }

    /**
     * Create/upgrade database when install/upgrade
     */
    public function install()
    {
        if (!Phpfox::getLib('database')->tableExists($this->_table_name)) {
            $this->createTable();
        } else {
            $this->_updateFields();
        }
    }

    /**
     * Update existing table
     */
    private function _updateFields()
    {
        $sOldFieldName = '';
        foreach ($this->_aFields as $field) {
            /**
             * @var Field $field
             */
            if (!Phpfox::getLib('database')->isField($this->_table_name, $field->getName())) {
                $aField = [
                    'table'     => $this->_table_name,
                    'field'     => $field->getName(),
                    'type'      => $field->getFullType(),
                    'attribute' => $field->getOther(),
                ];

                if (!empty($sOldFieldName)) {
                    $aField['after'] = $sOldFieldName;
                }
                //Add new field
                Phpfox::getLib('database')->addField($aField);
            } else {
                //Update an exist field
                $aParam = [
                    'type'           => $field->getFullType(),
                    'null'           => $field->getNull(),
                    'default'        => $field->getDefault(),
                    'extra'          => $field->getOther(),
                    'auto_increment' => is_a($this->_auto_increment, '\Core\App\Install\Database\Field') && ($this->_auto_increment->getName() == $field->getName()),
                ];
                Phpfox::getLib('database')->changeField($this->_table_name, $field->getName(), $aParam);
            }
            $sOldFieldName = $field->getName();
        }
    }

    /**
     * Check configuration set is correct
     *
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->_table_name)) {
            return false;
        }

        //a table at least have two fields
        if (count($this->_aFields) < 2) {
            return false;
        }

        //a table has to have a primary key
        if (!isset($this->_primary_key) || empty($this->_primary_key)) {
            return false;
        }

        foreach ($this->_aFields as $field) {
            if (!is_a($field, '\Core\App\Install\Database\Field')) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $aParam
     *
     * @return bool|int
     */
    public function insert($aParam)
    {
        foreach ($this->_aFieldParams as $sKey => $value) {
            if (!isset($value['auto_increment']) && !isset($aParam[$sKey])) {
                return false;
            }
        }
        $iId = Phpfox::getLib('database')->insert($this->_table_name, $aParam);
        return $iId;
    }

    /**
     * @param array $aConditions
     * @param bool  $bCount
     * @param null  $iLimit
     *
     * @return array
     */
    public function search($aConditions, $bCount = false, $iLimit = null)
    {
        $sConds = '';
        foreach ($aConditions as $sKey => $aCondition) {
            if (!isset($this->_aFieldParams[$sKey])) {
                continue;
            }
            if (!isset($aCondition['operator']) || !isset($aCondition['data'])) {
                continue;
            }
            if (!empty($sConds)) {
                $sConds .= ' AND ';
            }
            $sConds .= "`$sKey` " . $aCondition['operator'] . " \"" . $aCondition['data'] . "\"";
        }
        if (empty($sConds)) {
            $sConds = "true";
        }
        $oDb = Phpfox::getLib('database');
        if ($bCount) {
            $oDb->select('COUNT(*)');
        } else {
            $oDb->select('*');
        }
        $oDb->from($this->_table_name)
            ->where($sConds);
        if ($bCount) {
            $aResults = $oDb->execute('getSlaveField');
        } else {
            if (isset($iLimit) && $iLimit) {
                $oDb->limit($iLimit);
            }
            $aResults = $oDb->execute('getSlaveRows');
        }
        return $aResults;
    }
}
