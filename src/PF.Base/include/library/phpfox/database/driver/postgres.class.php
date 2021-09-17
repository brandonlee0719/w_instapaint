<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

Phpfox::getLibClass('phpfox.database.driver.mysqli');

/**
 * Postgres Database Driver
 * NOTE: This driver is not in use and is still being tested thus no documentation was done for it just yet.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @package 		Phpfox
 * @version 		$Id: postgres.class.php 1666 2010-07-07 08:17:00Z Raymond_Benc $
 */
class Phpfox_Database_Driver_Postgres extends Phpfox_Database_Driver_Mysqli
{
	public $sSlaveServer = '';

    protected $_hMaster = null;
    protected $_hSlave = null;
    protected $_sIsSlave = '';
    protected $_sLastQuery;
    protected $_aQuery = array();
		
	public function connect($sHost, $sUser, $sPass, $sName, $sPort = false, $sPersistent = false)
	{	
		$sConnection = "host='{$sHost}' dbname='{$sName}' user='{$sUser}' password='{$sPass}'";
		if ($sPort)
		{
			$sConnection .= " port='{$sPort}'";
		}
		
		$this->_hMaster = ($sPersistent ? @pg_pconnect($sConnection) : @pg_connect($sConnection));

		// Unable to connect to master
		if (!$this->_hMaster)
		{
			return Phpfox_Error::trigger('Cannot connect to the database:' . $this->_sqlError(), E_USER_ERROR);
		}
		
		// Check if we have any slave servers
		if (Phpfox::getParam(array('db', 'slave')))
		{
			// Get the slave array
			$aServers = Phpfox::getParam(array('db', 'slave_servers'));
			
			// Get a random slave to use if there is more then one slave
			$iSlave = (count($aServers) > 1 ? rand(0, (count($aServers) - 1)) : 0);
			
			if (PHPFOX_DEBUG)
			{
				$this->sSlaveServer = $aServers[$iSlave][0];
			}
			
			$sConnection = "host='{$aServers[$iSlave][0]}' dbname='{$sName}' user='{$aServers[$iSlave][1]}' password='{$aServers[$iSlave][2]}'";
			if (isset($aServers[$iSlave][3]))
			{
				$sConnection .= " port='{$aServers[$iSlave][3]}'";
			}			

			// Connect to slave
			$this->_hSlave = @pg_connect("host='{$aServers[$iSlave][0]}' dbname='{$sName}' user='{$aServers[$iSlave][1]}' password='{$aServers[$iSlave][2]}'");
			
			// Check if we were able to connect to the slave
			if ($this->_hSlave)
			{
				$this->_hSlave = null;
			}			
		}
		
		// If unable to connect to a slave or if no slave is called lets copy the master 
		if ($this->_hSlave === null)
		{
			$this->_hSlave =& $this->_hMaster;
		}		
		
		return true;
	}
	
	public function getVersion()
	{
		return @pg_parameter_status($this->_hMaster, 'server_version');
	}
	
	public function getServerInfo()
	{
		return 'PostgreSQL ' . $this->getVersion();
	}	

    /**
     * Performs sql query with error reporting and logging.
     * 
     * @access public
     * @param  string $sSql query string
     * @param  mixed $hLink
     * @return int query result handle
     */
	public function query($sSql, &$hLink = '')
    {
		if (!$hLink)
		{
			$hLink =& $this->_hMaster;	
		}
		
    	(PHPFOX_DEBUG  ? Phpfox_Debug::start('sql') : '');

		$this->_sLastQuery = $sSql;
    	$this->_hQuery = @pg_query($hLink, $sSql);

        if (!$this->_hQuery)
        {
        	Phpfox_Error::trigger('Query Error: ' . $this->_sqlError() . ' <br /> Query: ' . $sSql . '', (PHPFOX_DEBUG ? E_USER_ERROR : E_USER_WARNING));
        }        
     
        (PHPFOX_DEBUG ? Phpfox_Debug::end('sql', array('sql' => $sSql, 'slave' => $this->_sIsSlave, 'rows' => (is_bool($this->_hQuery) ? '-' : pg_num_rows($this->_hQuery)))) : '');
        
        $this->_sIsSlave = '';
        
        return $this->_hQuery;
    }    
    
    /** 
     * Returns one field from a row
     * 
     * @param string $sSql SQL query
     * @return mixed field value
     */
    public function getSlaveField($sSql)
    {
		$this->_sIsSlave = true;
		
    	return $this->_getField($sSql, $this->_hSlave);
    }     
    
    public function getSlaveRow($sSql, $bAssoc = true)
    {
    	$this->_sIsSlave = true;
    	
    	return $this->_getRow($sSql, $bAssoc, $this->_hSlave);
    }
    
    public function getSlaveRows($sSql, $bAssoc = true)
    {
    	$this->_sIsSlave = true;
    	
    	return $this->_getRows($sSql, $bAssoc, $this->_hSlave);
    }    
    
    public function getRow($sSql, $bAssoc = true)
    {
    	return $this->_getRow($sSql, $bAssoc, $this->_hMaster);
    }
    
    public function getRows($sSql, $bAssoc = true)
    {
    	return $this->_getRows($sSql, $bAssoc, $this->_hMaster);
    }    
    
    /** 
     * Returns one field from a row
     * 
     * @param string $sSql SQL query
     * @return mixed field value
     */
    public function getField($sSql)
    {
		return $this->_getField($sSql, $this->_hMaster);
    }    
    
    /**
     * Prepares string to store in db (performs  addslashes() )
     * 
     * @access	public
     * @param mixed $mParam string or array of string need to be escaped
     * @return mixed escaped string or array of escaped strings
     */
    public function escape($mParam)
    {
        if (is_array($mParam))
        {
            return array_map(array(&$this, 'escape'), $mParam);
		}

        $mParam = pg_escape_string($this->_hMaster, $mParam);

        return $mParam;
    }
    
    public function multiInsert($sTable, $aFields, $aValues)
    {
    	$sSql = "INSERT INTO {$sTable} (" . implode(', ', array_values($aFields)) . ") ";
    	$sSql .= " VALUES\n";
    	foreach ($aValues as $aValue)
    	{
    		$sSql .= "\n(";
    		foreach ($aValue as $mValue)
    		{
    			if (is_null($mValue))
    			{
    				$sSql .= "NULL, ";
    			}
    			else 
    			{
    				$sSql .= "'" . $this->escape($mValue) . "', ";
    			}
    		}
    		$sSql = rtrim(trim($sSql), ',');
    		$sSql .= "),";
    	}
    	$sSql = rtrim($sSql, ',');  
    	
        if ($hRes = $this->query($sSql))
        {
            return $this->getLastId();
		}
    	
    	return 0;
    } 
    
    /**
     * Performs insert of one row. Accepts values to insert as an array:
     *    'column1' => 'value1'
     *    'column2' => 'value2'
     * 
     * @access	public
     * @param string  $sTable    table name
     * @param array   $aValues   column and values to insert
     * @param boolean $bEscape true - method escapes values (with "), false - not escapes
     *                         * @param boolean $bReturnQuery
     * @return int last ID (or 0 on error)
     */
    public function insert($sTable, $aValues = [], $bEscape = true, $bReturnQuery = false)
    {
        if (!$aValues)
        {
            $aValues = $this->_aData;
        }

        $sValues = '';
        foreach ($aValues as $mValue)
        {
            if (is_null($mValue))
            {
                $sValues .= "NULL, ";
            }
            else
            {
                $sValues .= "'" . ($bEscape ? $this->escape($mValue) : $mValue) . "', ";
            }
        }
        $sValues = rtrim(trim($sValues), ',');

        if ($this->_aData)
        {
            $this->_aData = array();
        }


        $sSql = 'INSERT INTO '. $this->table($sTable) .' '.
                '        ('. implode(', ', array_keys($aValues)) .')'.
                ' VALUES ('.$sValues.')';
        if ($hRes = $this->query($sSql))
        {
        	if ($bReturnQuery)
        	{
        		return $sSql;
        	}

            return $this->getLastId();
		}

        return 0;
    }
    
    /**
     * Delete entry from the database
     * 
     * @access public
     * @param string $sTable is the table name
     * @param string $sQuery is the query we will run
     * @param integer $iLimit
     * @return int
     */
    public function delete($sTable, $sQuery, $iLimit = NULL)
    {
    	$this->query("DELETE FROM ". $this->table($sTable) ." WHERE ". $sQuery);
    }
    
    /**
     * Returns row id from last executed query
     * 
     * @access	public
     * @return int id of last INSERT operation
     */
    public function getLastId()
    {
    	static $aSeq = array();
    	
    	// Make sure we have a query to check
    	if (!empty($this->_sLastQuery))
    	{    		
    		// Only check "INSERT INTO"
    		if (preg_match("/^INSERT[\t\n ]+INTO[\t\n ]+([a-z0-9\_\-]+)/is", $this->_sLastQuery, $aTable))
			{
				// Lets get the seq. name
				$sTable = substr_replace($aTable[1], '', 0, strlen(Phpfox::getParam(array('db', 'prefix'))));				
				$sSeq = Phpfox::getParam(array('db', 'prefix')) . $sTable . '_seq';

				// Do we have a seq?
				if (!$aSeq)
				{
					// Store our seq. in the cache
					$oCache = Phpfox::getLib('cache');
					$sCacheId = $oCache->set('sql_postgres_seq');
					if (!($aSeq = $oCache->get($sCacheId)))
					{
						$sSql = "SELECT c.relname AS seqname					
								FROM pg_class c, pg_user u 
								WHERE c.relowner=u.usesysid AND c.relkind = 'S' 
								ORDER BY seqname";
						$aRows = $this->getRows($sSql);				
						foreach ($aRows as $aRow)
						{
							$aSeq[] = $aRow['seqname'];
						}
						$oCache->save($sCacheId, $aSeq);
					}
				}		

				// Do we have a valid seq?
				if (!in_array($sSeq, $aSeq))
				{
					return false;
				}				
				
				// Get the "last_value"
				$hQuery = @pg_query($this->_hMaster, "SELECT currval('{$sSeq}') AS last_value");

				if (!$hQuery)
				{
					return false;
				}

				$aRow = @pg_fetch_assoc($hQuery, null);

				@pg_free_result($this->_hQuery);

				return ($aRow) ? $aRow['last_value'] : false;
			}		
			
			$this->_sLastQuery = '';
    	}
    }

    public function freeResult()
	{
		if (is_resource($this->rQuery))
		{
			@pg_free_result($this->rQuery);
		}
	}
	
	public function affectedRows()
	{
		return @pg_affected_rows($this->rQuery);
	}

	public function search($sType, $mFields, $sSearch)
	{
		switch ($sType)
		{
			case 'full':		
				$sStr = 'AND (';		
				foreach ($mFields as $sField)
				{
					$sStr .= "" . $sField . " ~ '" . $this->escape($sSearch) . "' OR ";
				}
				$sStr = rtrim($sStr, 'OR ') . ')';
				break;
			case 'like%':
				$sStr = 'AND (';		
				foreach ($mFields as $sField)
				{
					$sStr .= "" . $sField . " LIKE '%" . $this->escape($sSearch) . "%' OR ";
				}
				$sStr = rtrim($sStr, 'OR ') . ')';				
				break;
            default:
                $sStr = '';
                break;
		}

		return $sStr;
	}	
	
	public function select($sSelect)
	{
		if (!isset($this->_aQuery['select']))
		{
			$this->_aQuery['select'] = 'SELECT ';
		}
		
		$this->_aQuery['select'] .= $sSelect;
		
		return $this;
	}
	
	public function where($aConds)
	{
		$this->_aQuery['where'] = '';
		if (is_array($aConds) && count($aConds))
		{
			foreach ($aConds as $sKey => $sValue)
			{
                if (is_string($sKey)) {
                    $this->_aQuery['where'] .= $this->_where($sKey, $sValue);

                    continue;
                }
                $this->_aQuery['where'] .= $sValue . ' ';
			}
			$this->_aQuery['where'] = "WHERE " . trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->_aQuery['where'])));
		}
		else 
		{
			if (!empty($aConds))
			{
				$this->_aQuery['where'] .= 'WHERE ' . $aConds;	
			}
		}
		
		return $this;
	}
	
	public function from($sTable, $sAlias = '')
	{
		$this->_aQuery['table'] = 'FROM ' . $this->table($sTable) . ($sAlias ? ' AS ' . $sAlias : '');
		
		return $this;
	}
	
	public function order($sOrder)
	{
        $sOrder = str_replace(['rand()', 'RAND()'], 'random()', $sOrder);
	    if (isset($this->_aQuery['order']))
        {
            $this->_aQuery['order'] .= ', ' . $sOrder;
        }
        else
        {
            $this->_aQuery['order'] = 'ORDER BY ' . $sOrder;
        }
		return $this;
	}

    public function group($sGroup, $bCanUseDistinctOn = false)
    {
        if ($bCanUseDistinctOn)
        {
            $this->_aQuery['group'] = '';
            if (isset($this->_aQuery['select']))
            {
                $this->_aQuery['select'] = str_replace('SELECT ', 'SELECT DISTINCT ON ( ' . $sGroup . ' ) ', $this->_aQuery['select']);
            }
            else
            {
                $this->_aQuery['select'] = 'SELECT DISTINCT ON ( ' . $sGroup . ' ) ';
            }
            if (isset($this->_aQuery['order']))
            {
                $this->_aQuery['order'] .= ', ' . $sGroup;
            }
            else
            {
                $this->_aQuery['order'] = 'ORDER BY ' . $sGroup;
            }
        }
        else
        {
            $this->_aQuery['group'] = 'GROUP BY ' . $sGroup;
        }

        return $this;
    }

	public function having($sHaving)
	{
		$this->_aQuery['having'] = 'HAVING ' . $sHaving;
		
		return $this;
	}	
	
	public function leftJoin($sTable, $sAlias, $mParam = null)
	{
		$this->_join('LEFT JOIN', $sTable, $sAlias, $mParam);
		
		return $this;
	}
	
	public function innerJoin($sTable, $sAlias, $mParam = null)
	{
		$this->_join('INNER JOIN', $sTable, $sAlias, $mParam);
		
		return $this;
	}		
	
	public function join($sTable, $sAlias, $mParam = null)
	{
		$this->_join('JOIN', $sTable, $sAlias, $mParam);
		
		return $this;
	}
	
	public function execute($sType = null, $aParams = array())
	{
		$sSql = $this->_aQuery['select'] . "\n";
        if (isset($this->_aQuery['table']))
        {
            $sSql .= $this->_aQuery['table'] . "\n";
        }

        if (isset($this->_aQuery['union_from']))
        {
            $sSql .= "FROM(\n";
        }

        if (!isset($aParams['union_no_check']) && count($this->_aUnions))
        {
            $iUnionCnt = 0;
            foreach ($this->_aUnions as $sUnion)
            {
                $iUnionCnt++;
                if ($iUnionCnt != 1)
                {
                    $sSql .= (isset($this->_aQuery['join_count']) ? ' + ' : ' UNION ');
                }

                $sSql .= '(' . $sUnion . ')';
            }
        }

        if (isset($this->_aQuery['join_count']))
        {
            $sSql .= ') AS total_count';
        }

        if (isset($this->_aQuery['union_from']))
        {
            $sSql .= ") AS " . $this->_aQuery['union_from'] . "\n";
        }

        $sSql .= (isset($this->_aQuery['join']) ? $this->_aQuery['join'] . "\n" : '');
		$sSql .= (isset($this->_aQuery['where']) ? $this->_aQuery['where'] . "\n" : '');
		$sSql .= (isset($this->_aQuery['group']) ? $this->_aQuery['group'] . "\n" : '');
		$sSql .= (isset($this->_aQuery['having']) ? $this->_aQuery['having'] . "\n" : '');
		$sSql .= (isset($this->_aQuery['order']) ? $this->_aQuery['order'] . "\n" : '');
		$sSql .= (isset($this->_aQuery['limit']) ? $this->_aQuery['limit'] . "\n" : '');	
		
		$this->_aQuery = array();
        if (!isset($aParams['union_no_check']))
        {
            $this->_aUnions = [];
        }

		$bDoCache = false;
		if (isset($aParams['cache']) && $aParams)
		{
			$bDoCache = true;	
			$oCache = Phpfox::getLib('cache');
		}
		
		if ($bDoCache && isset($oCache))
		{
			$sCacheId = $oCache->set($aParams['cache_name']);
			if ((isset($aParams['cache_limit']) && ($aRows = $oCache->get($sCacheId, $aParams['cache_limit']))) || ($aRows = $oCache->get($sCacheId)))
			{
				return $aRows;
			}
		}
		
		switch($sType)
		{
			case 'getSlaveRows':
				$aRows = $this->getSlaveRows($sSql);
				break;
			case 'getSlaveRow':
				$aRows = $this->getSlaveRow($sSql);
				break;				
			case 'getRow':
				$aRows = $this->getRow($sSql);
				break;	
			case 'getRows':
				$aRows = $this->getRows($sSql);
				break;	
			case 'getField':
				$aRows = $this->getField($sSql);
				break;				
			case 'getSlaveField':
				$aRows = $this->getSlaveField($sSql);
				break;
			default:
				return $sSql;
		}

		if ($bDoCache && isset($oCache) && isset($sCacheId) && isset($aRows))
		{
			$oCache->save($sCacheId, $aRows);
		}
		
		if (isset($aParams['free_result']))
		{
			$this->freeResult();
		}		
		
		return isset($aRows) ? $aRows : [];
	}
	
	function sqlReport($sQuery)
	{	
		$sHtml = '';
		$sExplainQuery = $sQuery;
		if (preg_match('/UPDATE ([a-z0-9_]+).*?WHERE(.*)/s', $sQuery, $m))
		{
			$sExplainQuery = 'SELECT * FROM ' . $m[1] . ' WHERE ' . $m[2];
		}
		elseif (preg_match('/DELETE FROM ([a-z0-9_]+).*?WHERE(.*)/s', $sQuery, $m))
		{
			$sExplainQuery = 'SELECT * FROM ' . $m[1] . ' WHERE ' . $m[2];
		}

		if (preg_match('/^SELECT/', $sExplainQuery))
		{
			$bTable = false;

			if ($hResult = @pg_query($this->_hMaster, "EXPLAIN $sExplainQuery"))
			{
				while ($aRow = @pg_fetch_assoc($hResult))
				{
					list($bTable, $sData) = Phpfox_Debug::addRow($bTable, $aRow);
					
					$sHtml .= $sData;
				}
			}
			@pg_free_result($hResult);

			if ($bTable)
			{
				$sHtml .= '</table>';
			}
		}
				
		return $sHtml;
	}	
	
	public function dropTables($aDrops, $aVals = array())
	{
		foreach ($aDrops as $sDrop)
		{
			$this->query("DROP TABLE {$sDrop}");		
		}		
		
		$sSql = "SELECT c.relname AS seqname					
				FROM pg_class c, pg_user u 
				WHERE c.relowner=u.usesysid AND c.relkind = 'S' 
				ORDER BY seqname";
		$aRows = $this->getRows($sSql);	
		$aSeq = array();	
		foreach ($aRows as $aRow)
		{
			if (substr($aRow['seqname'], 0, strlen(Phpfox::getParam(array('db', 'prefix')))) != Phpfox::getParam(array('db', 'prefix')))
			{
				continue;
			}
			$aSeq[] = $aRow['seqname'];
		}	
		
		foreach ($aSeq as $sSeq)
		{
			$this->query("DROP SEQUENCE {$sSeq}");
		}
	}

    /**
     * Adds an index to a table.
     *
     * @param string $sTable Database table.
     * @param string $sField List of indexes to add.
     * @param string $sName
     * @return int
     */
    public function addIndex($sTable, $sField, $sName = null)
    {

        $sSql = 'CREATE INDEX ' . ($sName !== null ? $sTable . $sName . ' ' : '') . 'ON ' . $sTable . ' (' . $sField . ')';

        return $this->query($sSql);
    }

    /**
     * Drop an index from a table
     *
     * @param string $sTable
     * @param null $sName
     *
     * @return int
     */
    public function dropIndex($sTable, $sName = null)
    {
        if ($sName === null)
        {
            return false;
        }

        $sSql = 'DROP INDEX ' . $sTable . $sName;

        return $this->query($sSql);
    }

    /**
     * Adds fields to a database table.
     *
     * @param array $aParams Array of fields and what type each field is.
     *
     * @return int
     */
    public function addField($aParams)
    {
        $type = Phpfox::getLib('database.export')->getType('postgres', $aParams['type']);
        $sSql = 'ALTER TABLE ' . $aParams['table'] . ' ADD COLUMN ' . $aParams['field'] . ' ' . $type;
        if (isset($aParams['attribute'])) {
            $aParams['attribute'] = str_replace(['unsigned', 'UNSIGNED'], "CHECK ({$aParams['field']} >= 0)", $aParams['attribute']);
            $sSql .= ' ' . $aParams['attribute'] . ' ';
        }
        if (isset($aParams['null'])) {
            $sSql .= ' ' . ($aParams['null'] ? 'NULL' : 'NOT NULL') . ' ';
        }
        if (isset($aParams['default'])) {
            $sSql .= ' DEFAULT ' . $aParams['default'] . ' ';
        }

        return $this->query($sSql);
    }

    /**
     * Drops a specific field from a table.
     *
     * @param string $sTable Database table
     * @param string $sField Name of the field to drop
     *
     * @return int
     */
    public function dropField($sTable, $sField)
    {
        return $this->query('ALTER TABLE ' . $sTable . ' DROP COLUMN ' . $sField);
    }

    /**
     * Checks if a field already exists or not.
     *
     * @param string $sTable Database table to check
     * @param string $sField Name of the field to check
     *
     * @return bool If the field exists we return true, if not we return false.
     */
    public function isField($sTable, $sField)
    {
        $aRows = $this->getRows("SELECT column_name from INFORMATION_SCHEMA.COLUMNS where table_name = '{$sTable}';");
        foreach ($aRows as $aRow) {
            if (strtolower($aRow['column_name']) == strtolower($sField)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Change field type.
     *
     * @param string $sTable Database table to check
     * @param string $sField Name of the field to check
     * @param array $aParams new params of the field
     * @return void
     *
     */

    public function changeField($sTable, $sField, $aParams)
    {
        $sSql = 'ALTER TABLE ' . $sTable . ' ALTER COLUMN ' . $sField;
        if (isset($aParams['type']))
        {
            $type = Phpfox::getLib('database.export')->getType('postgres', $aParams['type']);
            $this->query($sSql . ' TYPE ' . $type);
        }

        if (isset($aParams['null']))
        {
            $this->query($sSql . ($aParams['null'] ? ' DROP ' : ' SET ') . 'NOT NULL');
        }

        if (isset($aParams['default']))
        {
            $this->query($sSql . ' SET DEFAULT ' . $this->escape($aParams['default']));
        }
    }

    /**
     * Add primary key for table.
     *
     * @param string $sTable Database table
     * @param string $sField Name of the field
     *
     * @return int
     */
    public function addPrimaryKey($sTable, $sField)
    {
        return $this->query('ALTER TABLE ' . $sTable . ' ADD PRIMARY KEY (' . $sField . ')');
    }

    /**
     * truncate table
     *
     * @param $sTable
     *
     * @return int
     */
    public function truncateTable($sTable)
    {
        return $this->query('TRUNCATE TABLE ' . $this->table($sTable));
    }

    /**
     * drop table
     *
     * @param $sTable
     *
     * @return resource
     */
    public function dropTable($sTable)
    {
        return $this->query('DROP TABLE ' . $this->table($sTable));
    }

    /**
     * Returns the status of the table.
     *
     * @return array Returns information about the table in an array.
     */
    public function getTableStatus()
    {
        //TODO implement for postgres driver
        return false;
    }

    /**
     * Checks if a database table exists.
     *
     * @param string $sTable Table we are looking for.
     *
     * @return bool If the table exists we return true, if not we return false.
     */
    public function tableExists($sTable)
    {
        $sSql = 'SELECT * FROM information_schema.tables WHERE table_schema=\'public\' and table_type=\'' . $sTable . '\'';
        $aRows = $this->getRows($sSql);
        if (count($aRows) > 0) return true;

        return false;
    }

    /**
     * Optimizes a table
     *
     * @param string $sTable Table to optimize
     *
     * @return mixed
     */
    public function optimizeTable($sTable)
    {
        return false;
    }

    /**
     * Repairs a table
     *
     * @param string $sTable Table to repair
     *
     * @return mixed
     */
    public function repairTable($sTable)
    {
        return false;
    }

    /**
     * Checks if we can backup the database or not. This depends on the server itself.
     * We currently only support unix based servers.
     *
     * @return bool Returns true if we can backup or false if we can't
     */
    public function canBackup()
    {
        return false;
    }

    // TODO need to support add index on postgres and parse field type
    public function createTable($sName, $aFields, $bCheckExists = false)
    {
        $sSql = 'CREATE TABLE ' . ($bCheckExists ? 'IF NOT EXISTS ' : '') . $sName . "\n";
        $sSql .= '(' ."\n";
        $bHasPK = false;
        foreach ($aFields as $aField)
        {
            $sSql .= $aField['name'] . ' ' . $aField['type'] . ' ';
            if (isset($aField['auto_increment']) && $aField['auto_increment'])
            {
                $sSql .= "DEFAULT nextval('{$sName}_seq'),\n";

                // Make sure the sequence will be created before creating the table
                if (strpos($sSql, "CREATE SEQUENCE {$sName}_seq") === false)
                {
                    $sSql = "CREATE SEQUENCE {$sName}_seq;\n\n" . $sSql;
                }
            }
            $sSql .= (isset($aField['extra']) ? ' ' .$aField['extra'] : '' ) . ",\n";
            if (isset($aField['primary_key']) && $aField['primary_key'] == true)
            {
                $bHasPK = $aField['name'];
            }
        }

        if ($bHasPK !== false)
        {
            $sSql .= 'PRIMARY KEY (' . $bHasPK . ')' . "\n";
        }

        $sSql .= ')';

        $this->query($sSql);
    }

    public function getColumns($sTable)
    {
        $sSql = 'SELECT * FROM information_schema.tables WHERE table_schema=\'public\'';
        return $this->getRows($sSql);
    }

    protected function _join($sType, $sTable, $sAlias, $mParam = null)
	{
		if (!isset($this->_aQuery['join']))
		{
			$this->_aQuery['join'] = '';
		}
		$this->_aQuery['join'] .= $sType . " " . $this->table($sTable) . " AS " . $sAlias;
		if (is_array($mParam))
		{
            $this->_aQuery['join'] .= "\n\tON(";

            $sJoins = '';
            foreach ($mParam as $sKey => $sValue)
            {
                if (is_string($sKey)) {
                    //
                    $sJoins .= $this->_where($sKey, $sValue);

                    continue;
                }

                $sJoins .= $sValue . " ";
            }

            $this->_aQuery['join'] .= preg_replace("/^(AND|OR)(.*?)/i", "", trim($sJoins));
		}
		else 
		{
			$this->_aQuery['join'] .= "\n\tON({$mParam}";		
		}
		$this->_aQuery['join'] = preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->_aQuery['join'])) . ")\n";		
	}

    public function get() {
        return $this->execute('getRow');
    }
	
	private function _sqlError()
	{
		return pg_errormessage();
	}

    /**
     * Returns exactly one row as array. If there is number of rows
     * satisfying the condition then the first one will be returned.
     * 
     * @access	public
     * @param string $sSql   select query
     * @param string $bAssoc  type of returned rows array
     * @return array exact one row (first if multiply row selected): or false on error
     */
    protected function _getRow($sSql, $bAssoc, &$hLink)
    {   	
    	// Run the query
        $hRes = $this->query($sSql, $hLink);

        // Get the array
       	$aRes =  pg_fetch_array($hRes, null, ($bAssoc ? PGSQL_ASSOC : PGSQL_NUM));

        return $aRes ? $aRes : array();
    } 
    
    /**
     * Gets data returned by sql query
     * 
     * @access	public
     * @param string $sSql    select query
     * @param bool $bAssoc  type of returned rows array
     * @return array selected rows (each row is array of specified type) or emprt array on error
     */
    protected function _getRows($sSql, $bAssoc = true, &$hLink)
    {
        $aRows = array();
        $bAssoc = ($bAssoc ? PGSQL_ASSOC : PGSQL_NUM);

		// Run the query
        $this->rQuery = $this->query($sSql, $hLink);        
		
        // Put it into a while look
        while($aRow = pg_fetch_array($this->rQuery, null, $bAssoc))
        {
        	// Create an array for the data
            $aRows[] = $aRow;
        }

        return $aRows; //empty array on error
    }   
	
    /** 
     * Returns one field from a row
     * 
     * @param string $sSql SQL query
     * @return mixed field value
     */
    private function _getField($sSql, &$hLink)
    {
        $sRes = '';
        $aRow = $this->getRow($sSql, false, $hLink);
        if ($aRow)
        {
            $sRes = $aRow[0];
        }
        return $sRes;
    }

    public function ping()
    {
        return pg_ping($this->_hMaster);
    }

    public function reconnect($force_reconnect  = false)
    {
        if($force_reconnect OR !$this->ping()){
            $this->_hMaster = null;
            $this->_hSlave = null;
            return $this->connect(Phpfox::getParam(array('db', 'host')), Phpfox::getParam(array('db', 'user')), Phpfox::getParam(array('db', 'pass')), Phpfox::getParam(array('db', 'name')), Phpfox::getParam(array('db', 'port')));
        }

        return true;
    }
}