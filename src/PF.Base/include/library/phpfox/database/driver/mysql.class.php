<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Database driver for MySQL.
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @package           Phpfox
 * @version           $Id: mysql.class.php 3160 2011-09-21 10:46:04Z Miguel_Espinoza $
 */
class Phpfox_Database_Driver_Mysql extends Phpfox_Database_Dba
{
	/**
	 * IP/Host of the slave server we are currently using.
	 *
	 * @var string
	 */
	public $sSlaveServer;

	/**
	 * Resource for the MySQL master server
	 *
	 * @var \
	 */
	protected $_hMaster = null;

	/**
	 * Resource for the MySQL salve server
	 *
	 * @var resource
	 */
	protected $_hSlave = null;

	/**
	 * Check to see if we are using slave servers
	 *
	 * @var bool
	 */
	protected $_bIsSlave = false;

	/**
	 * Holds an array of all the MySQL functions we use. We store
	 * it here because we also provide support for MySQLi, which extends
	 * this class when it use.
	 *
	 * @var array
	 */
	protected $_aCmd = [
		'mysql_query'              => 'mysql_query',
		'mysql_connect'            => 'mysql_connect',
		'mysql_pconnect'           => 'mysql_pconnect',
		'mysql_select_db'          => 'mysql_select_db',
		'mysql_num_rows'           => 'mysql_num_rows',
		'mysql_fetch_array'        => 'mysql_fetch_array',
		'mysql_real_escape_string' => 'mysql_real_escape_string',
		'mysql_insert_id'          => 'mysql_insert_id',
		'mysql_fetch_assoc'        => 'mysql_fetch_assoc',
		'mysql_free_result'        => 'mysql_free_result',
		'mysql_error'              => 'mysql_error',
		'mysql_affected_rows'      => 'mysql_affected_rows',
		'mysql_get_server_info'    => 'mysql_get_server_info',
		'mysql_close'              => 'mysql_close',
	];

	/**
	 * Makes a connection to the MySQL database
	 *
	 * @param string $sHost       Hostname or IP
	 * @param string $sUser       User used to log into MySQL server
	 * @param string $sPass       Password used to log into MySQL server. This can be blank.
	 * @param string $sName       Name of the database.
	 * @param mixed  $sPort       Port number (int) or false by default since we do not need to define a port.
	 * @param bool   $sPersistent False by default but if you need a persistent connection set this to true.
	 *
	 * @return bool If we were able to connect we return true, however if it failed we return false and a error message
	 *              why.
	 */
	public function connect($sHost, $sUser, $sPass, $sName, $sPort = false, $sPersistent = false)
	{
		// Connect to master db
		$this->_hMaster = $this->_connect($sHost, $sUser, $sPass, $sPort, $sPersistent);

		// Unable to connect to master
		if (!$this->_hMaster) {
			// Cannot connect to the database
			return Phpfox_Error::set('Cannot connect to the database: ' . $this->_sqlError());
		}

		// Check if we have any slave servers
		if (Phpfox::getParam(['db', 'slave'])) {
			// Get the slave array
			$aServers = Phpfox::getParam(['db', 'slave_servers']);

			// Get a random slave to use if there is more then one slave
			$iSlave = (count($aServers) > 1 ? rand(0, (count($aServers) - 1)) : 0);

			if (PHPFOX_DEBUG) {
				$this->sSlaveServer = $aServers[ $iSlave ][0];
			}

			// Connect to slave
			$this->_hSlave = $this->_connect($aServers[ $iSlave ]['host'], $aServers[ $iSlave ]['user'], $aServers[ $iSlave ]['pass'], $aServers[ $iSlave ]['port'], $sPersistent);

			// Check if we were able to connect to the slave
			if ($this->_hSlave) {
				if (!@($this->_aCmd['mysql_select_db'] == 'mysqli_select_db' ? $this->_aCmd['mysql_select_db']($this->_hSlave, $sName) : $this->_aCmd['mysql_select_db']($sName, $this->_hSlave))) {
					$this->_hSlave = null;
				}
			}

		}

		// If unable to connect to a slave or if no slave is called lets copy the master 
		if (!$this->_hSlave) {
			$this->_hSlave =& $this->_hMaster;
		}

		// Attempt to connect to master table
		if (!@($this->_aCmd['mysql_select_db'] == 'mysqli_select_db' ? $this->_aCmd['mysql_select_db']($this->_hMaster, $sName) : $this->_aCmd['mysql_select_db']($sName, $this->_hMaster))) {
			return Phpfox_Error::set('Cannot connect to the database: ' . $this->_sqlError());
		}

		return true;
	}

	/**
	 * Returns the MySQL version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return @$this->_aCmd['mysql_get_server_info']($this->_hMaster);
	}

	/**
	 * Returns MySQL server information. Here we only identify that it is MySQL and the version being used.
	 *
	 * @return string
	 */
	public function getServerInfo()
	{
		return 'MySQL ' . $this->getVersion();
	}

	/**
	 * Performs sql query with error reporting and logging.
	 *
	 * @see mysql_query()
	 *
	 * @param  string  $sSql  MySQL query to perform
	 * @param resource $hLink MySQL resource. If nothing is passed we load the default master server.
	 *
	 * @return resource Returns the MYSQL resource from the function mysql_query()
	 */
	public function query($sSql, &$hLink = '')
	{
		if (!$hLink) {
			$hLink =& $this->_hMaster;
		}

		(PHPFOX_DEBUG ? Phpfox_Debug::start('sql') : '');

		$hRes = @($this->_aCmd['mysql_query'] == 'mysqli_query' ? $this->_aCmd['mysql_query']($hLink, $sSql) : $this->_aCmd['mysql_query']($sSql, $hLink));

		if (defined('PHPFOX_LOG_SQL') && Phpfox_File::instance()->isWritable(PHPFOX_DIR_FILE . 'log' . PHPFOX_DS)) {
			$log = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'phpfox_query_' . date('d.m.y', PHPFOX_TIME) . '_' . md5(Phpfox::getVersion()) . '.php';
			file_put_contents($log, "##\n{$sSql}##\n\n", FILE_APPEND);
		}

		if (!$hRes) {
			Phpfox_Error::trigger('Query Error:' . $this->_sqlError() . ' [' . $sSql . ']', (PHPFOX_DEBUG ? E_USER_ERROR : E_USER_WARNING));
		}

		(PHPFOX_DEBUG ? Phpfox_Debug::end('sql', ['sql' => $sSql, 'slave' => $this->_bIsSlave, 'rows' => (is_bool($hRes) ? '-' : @$this->_aCmd['mysql_num_rows']($hRes))]) : '');

		$this->_bIsSlave = false;

		return $hRes;
	}

	/**
	 * Prepares string to store in db (performs  addslashes() )
	 *
	 * @param mixed $mParam string or array of string need to be escaped
	 *
	 * @return mixed escaped string or array of escaped strings
	 */
	public function escape($mParam)
	{
		if (is_array($mParam)) {
			return array_map([&$this, 'escape'], $mParam);
		}

		$mParam = @($this->_aCmd['mysql_real_escape_string'] == 'mysqli_real_escape_string' ? $this->_aCmd['mysql_real_escape_string']($this->_hMaster, $mParam) : $this->_aCmd['mysql_real_escape_string']($mParam));

		return $mParam;
	}

	/**
	 * Returns row id from last executed query
	 *
	 * @return int id of last INSERT operation
	 */
	public function getLastId()
	{
		return @$this->_aCmd['mysql_insert_id']($this->_hMaster);
	}

	/**
	 * Frees the MySQL results
	 *
	 */
	public function freeResult()
	{
		if (is_resource($this->rQuery)) {
			@$this->_aCmd['mysql_free_result']($this->rQuery);
		}
	}

	/**
	 * Returns the affected rows.
	 *
	 * @return array
	 */
	public function affectedRows()
	{
		return @$this->_aCmd['mysql_affected_rows']($this->_hMaster);
	}

	/**
	 * MySQL has special search functions, so we try to use that here.
	 *
	 * @param string $sType   Type of search we plan on doing.
	 * @param mixed  $mFields Array of fields to search
	 * @param string $sSearch Value to search for.
	 *
	 * @return string MySQL query to use when performing the search
	 */
	public function search($sType, $mFields, $sSearch)
	{
		switch ($sType) {
			case 'full':
				return "AND MATCH(" . implode(',', $mFields) . ") AGAINST ('+" . $this->escape($sSearch) . "' IN BOOLEAN MODE)";
				break;
			case 'like%':
				$sSql = '';
				foreach ($mFields as $sField) {
					$sSql .= "OR " . $sField . " LIKE '%" . $this->escape($sSearch) . "%' ";
				}

				return 'AND (' . trim(ltrim(trim($sSql), 'OR')) . ')';
				break;
		}
	}

	/**
	 * During development you may need to check how your queries are being executed and how long they are taking. This
	 * routine uses MySQL's EXPLAIN to return useful information.
	 *
	 * @param string $sQuery MySQL query to check.
	 *
	 * @return string HTML output of the information we have found about the query.
	 */
	public function sqlReport($sQuery)
	{
		$sHtml = '';
		$sExplainQuery = $sQuery;
		if (preg_match('/UPDATE ([a-z0-9_]+).*?WHERE(.*)/s', $sQuery, $m)) {
			$sExplainQuery = 'SELECT * FROM ' . $m[1] . ' WHERE ' . $m[2];
		} elseif (preg_match('/DELETE FROM ([a-z0-9_]+).*?WHERE(.*)/s', $sQuery, $m)) {
			$sExplainQuery = 'SELECT * FROM ' . $m[1] . ' WHERE ' . $m[2];
		}

		$sExplainQuery = trim($sExplainQuery);

		if (preg_match('/SELECT/se', $sExplainQuery) || preg_match('/^\(SELECT/', $sExplainQuery)) {
			$bTable = false;

			if ($hResult = @($this->_aCmd['mysql_query'] == 'mysqli_query' ? $this->_aCmd['mysql_query']($this->_hMaster, "EXPLAIN $sExplainQuery") : $this->_aCmd['mysql_query']("EXPLAIN $sExplainQuery", $this->_hMaster))) {
				while ($aRow = @$this->_aCmd['mysql_fetch_assoc']($hResult)) {
					list($bTable, $sData) = Phpfox_Debug::addRow($bTable, $aRow);

					$sHtml .= $sData;
				}
			}
			@$this->_aCmd['mysql_free_result']($hResult);

			if ($bTable) {
				$sHtml .= '</table>';
			}
		}

		return $sHtml;
	}

	/**
	 * Check if a field in the database is set to null
	 *
	 * @param string $sField The field we plan to check
	 *
	 * @return string Returns MySQL IS NULL usage
	 */
	public function isNull($sField)
	{
		return '' . $sField . ' IS NULL';
	}

	/**
	 * Check if a field in the database is set not null
	 *
	 * @param string $sField The field we plan to check
	 *
	 * @return string Returns MySQL IS NOT NULL usage
	 */
	public function isNotNull($sField)
	{
		return '' . $sField . ' IS NOT NULL';
	}

	/**
	 * Adds an index to a table.
	 *
	 * @param string $sTable Database table.
	 * @param string $sField List of indexes to add.
	 * @param string $sName
	 * @return resource Returns the MySQL resource from mysql_query()
	 */
	public function addIndex($sTable, $sField, $sName =  null)
	{
	    if($sName) ;

		$sSql = 'ALTER TABLE ' . $sTable . ' ADD INDEX (' . $sField . ')';

		return $this->query($sSql);
	}

    /**
     * Drop an index from a table
     *
     * @param string $sTable
     * @param null $sName
     *
     * @return resource
     */
    public function dropIndex($sTable, $sName = null)
    {
        $sSql = 'ALTER TABLE ' . $sTable . ' DROP INDEX ' . $sName;

        return $this->query($sSql);
	}

	/**
	 * Adds fields to a database table.
	 *
	 * @param array $aParams Array of fields and what type each field is.
	 *
	 * @return resource Returns the MySQL resource from mysql_query()
	 */
	public function addField($aParams)
	{
        $type = Phpfox::getLib('database.export')->getType('mysql', $aParams['type']);
		$sSql = 'ALTER TABLE ' . $aParams['table'] . ' ADD ' . $aParams['field'] . ' ' . $type;
		if (isset($aParams['attribute'])) {
			$sSql .= ' ' . $aParams['attribute'] . ' ';
		}
		if (isset($aParams['null'])) {
			$sSql .= ' ' . ($aParams['null'] ? 'NULL' : 'NOT NULL') . ' ';
		}
		if (isset($aParams['default'])) {
			$sSql .= ' DEFAULT ' . $aParams['default'] . ' ';
		}

        if (isset($aParams['after'])) {
            $sSql .= ' AFTER ' . $aParams['after'] . ' ';
        }
        else if (isset($aParams['first'])) {
            $sSql .= ' FIRST ';
        }
		return $this->query($sSql);
	}

	/**
	 * Drops a specific field from a table.
	 *
	 * @param string $sTable Database table
	 * @param string $sField Name of the field to drop
	 *
	 * @return resource Returns the MySQL resource from mysql_query()
	 */
	public function dropField($sTable, $sField)
	{
		return $this->query('ALTER TABLE ' . $sTable . ' DROP ' . $sField);
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
	    $sTable=  $this->table($sTable);
		$aRows = $this->getRows("SHOW COLUMNS FROM {$sTable}");
		foreach ($aRows as $aRow) {
			if (strtolower($aRow['Field']) == strtolower($sField)) {
				return true;
			}
		}

		return false;
	}

    /**
     * Change field.
     *
     * @param string $sTable Database table to check
     * @param string $sField Name of the field to check
     * @param array $aParams new params for the field
     *
     * @return resource Returns the MySQL resource from mysql_query()
     */
    public function changeField($sTable, $sField, $aParams)
    {
        $sSql = 'ALTER TABLE ' . $this->table($sTable) . ' CHANGE ' . $sField . ' ' . $sField . ' ';
        if (isset($aParams['type'])) {
            $type = Phpfox::getLib('database.export')->getType('mysql', $aParams['type']);
            $sSql .= $type;
        }

        if (isset($aParams['null'])) {
            $sSql .= ' ' . ($aParams['null'] ? 'NULL' : 'NOT NULL');
        }

        if (isset($aParams['default'])) {
            $sSql .= ' DEFAULT ' . $this->escape($aParams['default']);
        }

        if (isset($aParams['extra']) && $aParams['extra']) {
            $sSql .= " $aParams[extra]";
        }

        if (isset($aParams['auto_increment']) && $aParams['auto_increment']) {
            $sSql .= ' AUTO_INCREMENT';
        }

        return $this->query($sSql);
    }

	/**
	 * Checks if an index already exists or not.
	 *
	 * @param string $sTable Database table to check
	 * @param string $sIndex Name of the index to check
	 *
	 * @return bool If the index exists we return true, if not we return false.
	 */
	public function isIndex($sTable, $sIndex)
	{
		$aRows = $this->getRows("SHOW INDEX FROM {$sTable}");
		foreach ($aRows as $aRow) {
			if (strtolower($aRow['Key_name']) == strtolower($sIndex)) {
				return true;
			}
		}

		return false;
	}

    /**
     * Add primary key for table.
     *
     * @param string $sTable Database table
     * @param string $sField Name of the field
     *
     * @return resource Returns the MySQL resource from mysql_query()
     */
	public function addPrimaryKey($sTable, $sField)
    {
        return $this->query('ALTER TABLE ' . $this->table($sTable) . ' ADD PRIMARY KEY (`' . $sField . '`)');
    }

    /**
     * truncate table
     *
     * @param $sTable
     *
     * @return resource
     */
    public function truncateTable($sTable)
    {
        return $this->query('TRUNCATE ' . $this->table($sTable));
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

    public function getColumns($sTable)
    {
        return $this->getRows("SHOW COLUMNS FROM {$sTable}");
    }

    /**
	 * Returns the status of the table.
	 *
	 * @return array Returns information about the table in an array.
	 */
	public function getTableStatus()
	{
		return $this->_getRows('SHOW TABLE STATUS', true, $this->_hMaster);
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
		$aTables = $this->getTableStatus();

		foreach ($aTables as $aTable) {
			if ($aTable['Name'] == $sTable) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Optimizes a table
	 *
	 * @param string $sTable Table to optimize
	 *
	 * @return resource Returns the MySQL resource from mysql_query()
	 */
	public function optimizeTable($sTable)
	{
		return $this->query('OPTIMIZE TABLE ' . $this->escape($sTable));
	}

	/**
	 * Repairs a table
	 *
	 * @param string $sTable Table to repair
	 *
	 * @return resource Returns the MySQL resource from mysql_query()
	 */
	public function repairTable($sTable)
	{
		return $this->query('REPAIR TABLE ' . $this->escape($sTable));
	}

	/**
	 * Checks if we can backup the database or not. This depends on the server itself.
	 * We currently only support unix based servers.
	 *
	 * @return bool Returns true if we can backup or false if we can't
	 */
	public function canBackup()
	{
		return ((function_exists("exec") AND $checkDump = @str_replace("mysqldump:", "", exec("whereis mysqldump")) AND !empty($checkDump)) ? true : false);
	}

	/**
	 * Performs a backup of the database and places the backup in a specific area on the server
	 * based on what the admins decide.
	 *
	 * @param string $sPath Full path to where to place the backup.
	 *
	 * @return string Full path to where the backup is located including the file name.
	 */
	public function backup($sPath)
	{
		if (!is_dir($sPath)) {
			return Phpfox_Error::set(_p('the_path_you_provided_is_not_a_valid_directory'));
		}

		if (!Phpfox_File::instance()->isWritable($sPath, true)) {
			return Phpfox_Error::set(_p('the_path_you_provided_is_not_a_valid_directory'));
		}

		$sPath = rtrim($sPath, PHPFOX_DS) . PHPFOX_DS;
		$sFileName = uniqid() . '.sql';
		$sZipName = 'sql-backup-' . date('Y-d-m', PHPFOX_TIME) . '-' . uniqid() . '.tar.gz';

		shell_exec("mysqldump --skip-add-locks --disable-keys --skip-comments -h" . Phpfox::getParam(['db', 'host']) . " -u" . Phpfox::getParam(['db', 'user']) . " -p" . Phpfox::getParam(['db', 'pass']) . " " . Phpfox::getParam(['db', 'name']) . " > " . $sPath . $sFileName . "");
		chdir($sPath);
		shell_exec("tar -czf " . $sZipName . " " . $sFileName . "");
		chdir(PHPFOX_DIR);
		unlink($sPath . $sFileName);

		return $sPath . $sZipName;
	}

	/**
	 * Close the SQL connection
	 *
	 * @return bool TRUE on success, FALSE on failure
	 */
	public function close()
	{
		return @$this->_aCmd['mysql_close']($this->_hMaster);
	}


	/**
	 * Returns exactly one row as array. If there is number of rows
	 * satisfying the condition then the first one will be returned.
	 *
	 * @param string $sSql   select query
	 * @param string $bAssoc type of returned rows array
	 *
	 * @return array exact one row (first if multiply row selected): or false on error
	 */
	protected function _getRow($sSql, $bAssoc, &$hLink)
	{
		// Run the query
		$hRes = $this->query($sSql, $hLink);

		// Get the array
		$aRes = $this->_aCmd['mysql_fetch_array']($hRes, ($bAssoc ? MYSQL_ASSOC : MYSQL_NUM));

		return $aRes ? $aRes : [];
	}

	/**
	 * Gets data returned by sql query
	 *
	 * @param string $sSql   select query
	 * @param bool $bAssoc type of returned rows array
	 *
	 * @return array selected rows (each row is array of specified type) or empty array on error
	 */
	protected function _getRows($sSql, $bAssoc = true, &$hLink)
	{
		$aRows = [];
		$bAssoc = ($bAssoc ? MYSQL_ASSOC : MYSQL_NUM);

		// Run the query
		$this->rQuery = $this->query($sSql, $hLink);

		// Put it into a while look
		while ($aRow = $this->_aCmd['mysql_fetch_array']($this->rQuery, $bAssoc)) {
			// Create an array for the data
			$aRows[] = $aRow;
		}

		return $aRows; //empty array on error
	}

	/**
	 * Makes a connection to the MySQL database
	 *
	 * @param string $sHost       Hostname or IP
	 * @param string $sUser       User used to log into MySQL server
	 * @param string $sPass       Password used to log into MySQL server. This can be blank.
	 * @param mixed  $sPort       Port number (int) or false by default since we do not need to define a port.
	 * @param bool   $sPersistent False by default but if you need a persistent connection set this to true.
	 *
	 * @return bool If we were able to connect we return true, however if it failed we return false and a error message
	 *              why.
	 */
	protected function _connect($sHost, $sUser, $sPass, $sPort = false, $sPersistent = false)
	{
		if ($sPort) {
			$sHost = $sHost . ':' . $sPort;
		}

		if ($hLink = ($sPersistent ? @$this->_aCmd['mysql_pconnect']($sHost, $sUser, $sPass) : @$this->_aCmd['mysql_connect']($sHost, $sUser, $sPass))) {
		    mysql_query("SET SQL_MODE = ''", $hLink);
			return $hLink;
		}

		return false;
	}

	/**
	 * Returns any SQL errors.
	 *
	 * @return string String of error message in case something failed.
	 */
	private function _sqlError()
	{
		return ($this->_aCmd['mysql_error'] == 'mysqli_error' ? @$this->_aCmd['mysql_error']($this->_hMaster) : @$this->_aCmd['mysql_error']());
	}

	/**
	 * Begin transaction
	 *
	 * @return mixed
	 */
	public function beginTransaction()
	{
		mysql_query("START TRANSACTION", $this->_hMaster);
	}

	/**
	 * Rollback a transaction
	 */
	public function rollback()
	{
		return mysql_query('ROLLBACK', $this->_hMaster);
	}

	/**
	 * Commit a transaction
	 */
	public function commit()
	{
		return mysql_query('COMMIT', $this->_hMaster);
	}

	/**
	 * @inheritdoc
	 */
	public function forUpdate()
	{
		$this->_aQuery['for_update'] = true;
		return $this;
	}

    const SELECT_FOR_COUNT = 1;

    const SELECT_COUNTING = 2;

    /**
     * Array of all the parts of a query we are going to execute
     *
     * @see self::execute()
     * @var array
     */
    protected $_aQuery = array();

    protected $_sSingleData = '';

    /**
     * @var int : 0, 1, 2
     */
    protected $_countState = false;

    /**
     * Array of all the words that cannot be used
     * when creating a database table or field. This is only
     * used in development mode.
     *
     * @var array
     */
    protected $_aWords = array();

    /**
     * Holds all the data that has been filtered when inserting or updating
     * information directly from a from posted by an end user.
     *
     * @var array
     */
    protected $_aData = array();

    /**
     * Array of all the SQL unions.
     *
     * @var array
     */
    protected $_aUnions = array();

    /**
     * Class constructor. If we are in development mode we store
     * all the words that cannot be used when creating tables or fields.
     *
     */
    public function __construct()
    {

    }

    /**
     * Returns one field from a row using a slave connection
     *
     * @param string $sSql SQL query
     * @return resource SQL resource
     */
    public function getSlaveField($sSql)
    {
        $this->_bIsSlave = true;

        return $this->_getField($sSql, $this->_hSlave);
    }

    /**
     * Returns one row using a slave connection
     *
     * @param string $sSql SQL query
     * @param bool $bAssoc True to return an associative array
     * @return resource SQL resource
     */
    public function getSlaveRow($sSql, $bAssoc = true)
    {
        $this->_bIsSlave = true;

        return $this->_getRow($sSql, $bAssoc, $this->_hSlave);
    }

    /**
     * Returns several rows using a slave connection
     *
     * @param string $sSql SQL query
     * @param bool $bAssoc True to return an associative array
     * @return array resource SQL resource
     */
    public function getSlaveRows($sSql, $bAssoc = true)
    {
        $this->_bIsSlave = true;

        return $this->_getRows($sSql, $bAssoc, $this->_hSlave);
    }

    /**
     * Returns one row
     *
     * @param string $sSql SQL query
     * @param bool $bAssoc True to return an associative array
     * @return resource SQL resource
     */
    public function getRow($sSql, $bAssoc = true)
    {
        return $this->_getRow($sSql, $bAssoc, $this->_hMaster);
    }

    /**
     * Returns several rows
     *
     * @param string $sSql SQL query
     * @param bool $bAssoc True to return an associative array
     * @return resource SQL resource
     */
    public function getRows($sSql, $bAssoc = true)
    {
        return $this->_getRows($sSql, $bAssoc, $this->_hMaster);
    }

    /**
     * Returns one field from a row
     *
     * @param string $sSql SQL query
     * @return resource SQL resource
     */
    public function getField($sSql)
    {
        return $this->_getField($sSql, $this->_hMaster);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        $this->_countState = self::SELECT_COUNTING;

        return intval($this->execute('getField'));
    }

    /**
     * Stores the SELECT part of a query
     *
     * @see self::execute()
     * @param string $sSelect Select part of an SQL query
     * @return Phpfox_Database_Dba
     */
    public function select($sSelect)
    {
        /*
        if ($sSelect == 'dob_setting')
        {
            echo $a;
        }
        */
        if (!isset($this->_aQuery['select']))
        {
            $this->_aQuery['select'] = 'SELECT ';
        }

        $this->_aQuery['select'] .= $sSelect;

        return $this;
    }

    /**
     * @param $file_name
     * @return Phpfox_Database_Dba
     */
    public function singleData($file_name)
    {
        $this->_sSingleData = $file_name;
        return $this;
    }
    /**
     * Stores the WHERE part of a query
     * Example using a string method:
     * <code>
     * ->where('user_id = 1')
     * </code>
     * Example using an array method:
     * <code>
     * $aCond = array();
     * $aCond[] = 'AND user_id = 1';
     * $aCond[] = 'AND email = \'foo@bar.com\'';
     * ->where($aCond)
     * </code>
     *
     * @see self::execute()
     * @param mixed $aConds Can be a string of the WHERE part of an SQL query or an array or all the parts of an SQL query.
     * @return Phpfox_Database_Dba
     */
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

    /**
     * Stores the FROM part of a query
     *
     * @see self::execute()
     * @param string $sTable Table to query
     * @param string $sAlias Optional usage of alias can be passed here
     * @return Phpfox_Database_Dba
     */
    public function from($sTable, $sAlias = '')
    {
        if (PHPFOX_DEBUG && in_array(strtoupper($sAlias), $this->_aWords))
        {
            Phpfox_Error::trigger('The alias "' . $sAlias . '" is a reserved SQL word. Use another alias to resolve this problem.', E_USER_ERROR);
        }

        $this->_aQuery['table'] = 'FROM ' . $this->table($sTable) . ($sAlias ? ' AS ' . $sAlias : '');

        return $this;
    }

    public function table($sTable) {
        if (substr($sTable, 0, 1) == ':') {
            $sTable = Phpfox::getT(str_replace(':', '', $sTable));
        }
        return $sTable;
    }

    /**
     * Stores the ORDER part of a query
     *
     * @see self::execute()
     * @param string $sOrder SQL ORDER BY command
     * @return Phpfox_Database_Dba
     */
    public function order($sOrder)
    {
        if (!empty($sOrder))
        {
            $this->_aQuery['order'] = 'ORDER BY ' . $sOrder;
        }

        return $this;
    }

    /**
     * Stores the GROUP BY part of a query
     *
     * @see self::execute()
     * @param string $sGroup SQL GROUP BY command
     * @param bool $bCanUseDistinctOn
     * @return Phpfox_Database_Dba
     */
    public function group($sGroup, $bCanUseDistinctOn = false)
    {
        $this->_aQuery['group'] = 'GROUP BY ' . $sGroup;

        return $this;
    }

    /**
     * Stores the HAVING part of a query
     *
     * @see self::execute()
     * @param string $sHaving SQL HAVING command
     * @return Phpfox_Database_Dba
     */
    public function having($sHaving)
    {
        $this->_aQuery['having'] = 'HAVING ' . $sHaving;

        return $this;
    }

    /**
     * Creates a LEFT JOIN for an SQL query.
     * Example of left joining tables:
     * <code>
     * Phpfox_Database::instance()->select('*')
     * 		->from('user', 'u')
     * 		->leftJoin('user_info', 'ui', 'ui.user_id = u.user_id')
     * 		->execute('getRows');
     * </code>
     *
     * @see self::_join()
     * @param string $sTable Table to join
     * @param string $sAlias Alias to use to identify the table and make it unique
     * @param mixed $mParam Can be a string or an array of how to link the tables. This is usually a string that contains the part found with an SQL ON(__STRING__)
     * @return Phpfox_Database_Dba
     */
    public function leftJoin($sTable, $sAlias, $mParam = null)
    {
        $this->_join('LEFT JOIN', $sTable, $sAlias, $mParam);

        return $this;
    }

    /**
     * Creates a INNER JOIN for an SQL query.
     * Example of left joining tables:
     * <code>
     * Phpfox_Database::instance()->select('*')
     * 		->from('user', 'u')
     * 		->innerJoin('user_info', 'ui', 'ui.user_id = u.user_id')
     * 		->execute('getRows');
     * </code>
     *
     * @see self::_join()
     * @param string $sTable Table to join
     * @param string $sAlias Alias to use to identify the table and make it unique
     * @param mixed $mParam Can be a string or an array of how to link the tables. This is usually a string that contains the part found with an SQL ON(__STRING__)
     * @return Phpfox_Database_Dba
     */
    public function innerJoin($sTable, $sAlias, $mParam = null)
    {
        $this->_join('INNER JOIN', $sTable, $sAlias, $mParam);

        return $this;
    }

    /**
     * Creates a JOIN for an SQL query.
     * Example of left joining tables:
     * <code>
     * Phpfox_Database::instance()->select('*')
     * 		->from('user', 'u')
     * 		->join('user_info', 'ui', 'ui.user_id = u.user_id')
     * 		->execute('getRows');
     * </code>
     *
     * @see self::_join()
     * @param string $sTable Table to join
     * @param string $sAlias Alias to use to identify the table and make it unique
     * @param mixed $mParam Can be a string or an array of how to link the tables. This is usually a string that contains the part found with an SQL ON(__STRING__)
     * @return Phpfox_Database_Dba
     */
    public function join($sTable, $sAlias, $mParam = null)
    {
        $this->_join('JOIN', $sTable, $sAlias, $mParam);

        return $this;
    }

    /**
     * Stores the LIMIT/OFFSET part of a query. It can also be used
     * to create a pagination if params 2 and 3 and filled otherwise
     * it bahaves just as a limit on the SQL query.
     *
     * @see self::execute()
     * @param int $iPage If $sLimit and $iCnt are NULL then this value is the LIMIT on the SQL query. However if $sLimit and $iCnt are not NULL then this value is the current page we are on.
     * @param string $sLimit Is how many to limit per query
     * @param int $iCnt Is how many rows there are in this query
     * @param bool $bCorrectMax Should we limit searches to valid pages
     * @return Phpfox_Database_Dba
     */
    public function limit($iPage, $sLimit = null, $iCnt = null, $bReturn = false, $bCorrectMax = false)
    {
        // $bCorrectMax = false;
        if ($sLimit === null && $iCnt === null && $iPage !== null)
        {
            $this->_aQuery['limit'] = 'LIMIT ' . $iPage;

            return $this;
        }

        if ($bCorrectMax == true)
        {
            $iOffset = ($iCnt === null ? $iPage : Phpfox_Pager::instance()->getOffset($iPage, $sLimit, $iCnt));
            $this->_aQuery['limit'] = ($sLimit ? 'LIMIT ' . $sLimit : '') . ($iOffset ? ' OFFSET ' . $iOffset : '');
        }
        else
        {
            $this->_aQuery['limit'] = ($sLimit ? 'LIMIT ' . $sLimit : '') . ($sLimit != null && $iPage > 0 ? ' OFFSET ' . (($iPage-1)* ($sLimit)) : '');
        }

        if ($bReturn === true)
        {
            return $this->_aQuery['limit'];
        }

        return $this;
    }

    /**
     * Build a UNION call.
     *
     * @return Phpfox_Database_Dba
     */
    public function union()
    {
        $this->_aUnions[] = $this->execute(null, array('union_no_check' => true));

        return $this;
    }

    /**
     * Build a UNION FROM call.
     *
     * @param string $sAlias FROM alias name.
     * @param bool $bUnionAll
     * @return Phpfox_Database_Dba
     */
    public function unionFrom($sAlias, $bUnionAll = false)
    {
        $this->_aQuery['union_from'] = $sAlias;
        $this->_aQuery['union_all'] = $bUnionAll;

        return $this;
    }

    /**
     * Define that this is a joined count
     *
     * @return Phpfox_Database_Dba
     */
    public function joinCount()
    {
        $this->_aQuery['join_count'] = true;

        return $this;
    }

    /**
     * please use function executeRow
     *
     * @deprecated from 4.6.0
     *
     * @return array|int|string
     */
    public function get() {
        return $this->execute('getRow');
    }

    /**
     * please use function executeRows
     *
     * @deprecated from 4.6.0
     *
     * @return array|int|string
     */
    public function all() {
        return $this->execute('getRows');
    }

    /**
     * please use function executeField
     *
     * @deprecated from 4.6.0
     *
     * @return array|int|string
     */
    public function count() {
        return $this->execute('getField');
    }

    /**
     * @see $this->execute();
     *
     * @param bool $bSlave
     *
     * @return int|string
     */
    public function executeField($bSlave = true)
    {
        $sGet = $bSlave ? "getSlaveField" : "getField";
        return $this->execute($sGet);
    }

    /**
     * @see $this->execute();
     *
     * @param bool $bSlave
     *
     * @return array
     */
    public function executeRow($bSlave = true)
    {
        $sGet = $bSlave ? "getSlaveRow" : "getRow";
        return $this->execute($sGet);
    }

    /**
     * @see $this->execute();
     *
     * @param bool $bSlave
     *
     * @return array
     */
    public function executeRows($bSlave = true)
    {
        $sGet = $bSlave ? "getSlaveRows" : "getRows";
        return $this->execute($sGet);
    }

    /**
     * Performs the final SQL query with all the information we have gathered from various
     * other methods in this class. Via this method you can perform all tasks from getting
     * a single field from a row, to just one row or a list of rows.
     *
     * @see self::getRow()
     * @see self::getRows()
     * @see self::getField()
     *
     * @param string $sType   The command we plan to execute. It can also be NULL or empty and will simply return the SQL query itself without executing it.
     * @param array  $aParams Any special commands that we need to run can be passed here. Mainly used if we were to cache the actual query.
     *
     * @return int|string|array Depending on the command you ran this can return various things, usually an array but it all depends on what you executed.
     */
    public function execute($sType = null, $aParams = array())
    {
        if (($sType == 'getField' || $sType == 'getSlaveField') && (!isset($this->_aQuery['limit']) || empty($this->_aQuery['limit'])))
        {
            $this->_aQuery['limit'] = ' LIMIT 1';
        }
        $sSql = '';

        if($this->_countState != self::SELECT_COUNTING){
            if (isset($this->_aQuery['select']))
            {
                $sSql .= $this->_aQuery['select'] . "\n";
            }

            if (isset($this->_aQuery['join_count']))
            {
                $sSql .= 'SELECT (';
            }
        } else {
            if(empty($this->_aQuery['group'])) {
                $sSql  .= 'SELECT count(*) as total_rows ';
            } else {
                $sSql  .= 'SELECT count(DISTINCT '.substr($this->_aQuery['group'],9).') as total_rows ';
            }
        }

        if (isset($this->_aQuery['table']))
        {
            $sSql .= $this->_aQuery['table'] . "\n";
        }

        if (isset($this->_aQuery['forceIndex']) && !empty($this->_aQuery['forceIndex']))
        {
            $sSql .= 'FORCE INDEX (' . $this->_aQuery['forceIndex'] .') ' . "\n";
        }

        if (isset($this->_aQuery['union_from']))
        {
            $sSql .= "FROM(\n";
        }

        if (!isset($aParams['union_no_check']) && count($this->_aUnions))
        {
            $iUnionCnt = 0;
            $sUnionType = (isset($this->_aQuery['union_all']) && $this->_aQuery['union_all']) ? ' UNION ALL ' : ' UNION ';
            foreach ($this->_aUnions as $sUnion)
            {
                $iUnionCnt++;
                if ($iUnionCnt != 1)
                {
                    $sSql .= (isset($this->_aQuery['join_count']) ? ' + ' : $sUnionType);
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
        if($this->_countState != self::SELECT_COUNTING){
            $sSql .= (isset($this->_aQuery['group']) ? $this->_aQuery['group'] . "\n" : '');
        }

        $sSql .= (isset($this->_aQuery['having']) ? $this->_aQuery['having'] . "\n" : '');

        if($this->_countState != self::SELECT_COUNTING)
        {
            $sSql .= (isset($this->_aQuery['order']) ? $this->_aQuery['order'] . "\n" : '');
            $sSql .= (isset($this->_aQuery['limit']) ? $this->_aQuery['limit'] . "\n" : '');
            $sSql .= (isset($this->_aQuery['for_update']) ? '  FOR UPDATE ' . "\n" : '');

        }
        $sSql .= PHP_EOL;

        if (method_exists($this, '_execute'))
        {
            $sSql = $this->_execute();
        }

        if($this->_countState != self::SELECT_FOR_COUNT) {
            $this->_aQuery = [];

            if (!isset($aParams['union_no_check']))
            {
                $this->_aUnions = array();
            }
        }

        $bDoCache = false;
        if (isset($aParams['cache']) && !empty($aParams))
        {
            $bDoCache = true;
            $oCache = Phpfox::getLib('cache');
        }

        if ($bDoCache)
        {
            $sCacheId = $oCache->set($aParams['cache_name']);
            if ((isset($aParams['cache_limit']) && ($aRows = $oCache->get($sCacheId, $aParams['cache_limit']))) || ($aRows = $oCache->get($sCacheId)))
            {
                if (!empty($this->_sSingleData))
                {
                    return $this->_singleData($aRows);
                } else
                {
                    return $aRows;
                }
            }
        }

        if($this->_countState == self::SELECT_COUNTING)
            $this->_countState =  false;

        $sType = strtolower($sType);

        switch($sType)
        {
            case 'getslaverows':
                $aRows = $this->getSlaveRows($sSql);
                break;
            case 'getslaverow':
                $aRows = $this->getSlaveRow($sSql);
                break;
            case 'getrow':
                $aRows = $this->getRow($sSql);
                break;
            case 'getrows':
                $aRows = $this->getRows($sSql);
                break;
            case 'getfield':
                $aRows = $this->getField($sSql);
                break;
            case 'getslavefield':
                $aRows = $this->getSlaveField($sSql);
                break;
            default:
                return $sSql;
                break;
        }
        if ($bDoCache)
        {
            $oCache->save($sCacheId, $aRows);
        }

        if (isset($aParams['free_result']))
        {
            $this->freeResult();
        }
        if (!empty($this->_sSingleData))
        {
            return $this->_singleData($aRows);
        } else
        {
            return $aRows;
        }
    }

    /**
     * We clean out the query we just ran so another query can be built
     *
     */
    public function clean()
    {
        $this->_aQuery = array();
    }

    /**
     * Process data from a form a end-user posted and prepare it to be used when inserting/updating records
     *
     * @param array $aFields Array of rules of the fields that are allowed and the type it must be
     * @param array $aVals  $_POST fields from a form
     * @return Phpfox_Database_Dba
     */
    public function process($aFields, $aVals)
    {
        foreach ($aFields as $mKey => $mVal)
        {
            if (is_numeric($mKey))
            {
                unset($aFields[$mKey]);

                $mKey = $mVal;
                $mVal = 'string';
            }

            if (empty($aVals[$mKey]))
            {
                $aVals[$mKey] = ($mVal == 'int' ? 0 : null);
            }

            $aFields[$mKey] = $mVal;
        }

        foreach ($aVals as $mKey => $mVal)
        {
            if (!isset($aFields[$mKey]))
            {
                continue;
            }

            $this->_aData[$mKey] = ($aFields[$mKey] == 'int' ? (int) $mVal : $mVal);
        }

        return $this;
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
     * @param boolean $bReturnQuery
     * @return int last ID (or 0 on error)
     */
    public function insert($sTable, $aValues = array(), $bEscape = true, $bReturnQuery = false)
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

        $sSql = $this->_insert($this->table($sTable), implode(', ', array_keys($aValues)), $sValues);

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
     * Runs an SQL query to run one SQL query and insert multiple rows. The 2nd and 3rd
     * params much match in order to inser the data correctly.
     *
     * @param string $sTable Table to insert the data
     * @param array $aFields Array of table fields
     * @param array $aValues Array of values to insert that matches the table fields
     * @return int Returns the last ID of the insert. Usually the auto_increment.
     */
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
     * Performs update of rows.
     *
     * @param string $sTable  table name
     * @param array  $aValues array of column=>new_value
     * @param string $sCond   condition (without WHERE)
     * @param boolean $bEscape true - method escapes values (with "), false - not escapes
     * @return boolean|resource true - update successfully, false - error
     */
    public function update($sTable, $aValues = array(), $sCond = null, $bEscape = true)
    {
        if (!is_array($aValues) && count($this->_aData))
        {
            $sCond = $aValues;
            $aValues = $this->_aData;
            $this->_aData = array();
        }

        if (is_array($sCond)) {
            $aClone = $sCond;
            $sCond = '';
            foreach ($aClone as $sKey => $sValue) {
                $sCond .= $this->_where($sKey, $sValue);
            }
            $sCond = trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($sCond)));
        }

        $sSets = '';
        foreach ($aValues as $sCol => $sValue) {
            $sCmd = "=";
            if (is_array($sValue)) {
                $sCmd = $sValue[0];
                $sValue = $sValue[1];
            }

            $sSets .= "{$sCol} {$sCmd} " . (is_null($sValue) ? 'NULL' : ($bEscape ? "'" . $this->escape($sValue) . "'" : $sValue)) . ", ";
        }
        $sSets[strlen($sSets)-2] = '  ';

        return $this->query($this->_update($this->table($sTable), $sSets, $sCond));
    }

    /**
     * Delete entry from the database
     *
     * @param string $sTable is the table name
     * @param string|array $sQuery is the query we will run
     * @return boolean true - update successfule, false - error
     */
    public function delete($sTable, $sQuery, $iLimit = null)
    {
        if (is_array($sQuery)) {
            $sCond = '';
            foreach ($sQuery as $sKey => $sValue) {
                $sCond .= $this->_where($sKey, $sValue);
            }
            $sQuery = trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($sCond)));
        }

        if ($iLimit !== null)
        {
            $sQuery .= ' LIMIT ' . (int) $iLimit;
        }

        return $this->query("DELETE FROM {$this->table($sTable)} WHERE ". $sQuery);
    }

    /**
     * Drops tables from the database
     *
     * @param string $aDrops Array of tables to drop
     * @param array $aVals Not being used at the moment.
     */
    public function dropTables($aDrops, $aVals = array())
    {
        if (!is_array($aDrops))
        {
            $aDrops = array($aDrops);
        }
        foreach ($aDrops as $sDrop)
        {
            $this->query("DROP TABLE IF EXISTS {$sDrop}");
        }
    }

    /**
     * Updates a int field in the database to increase or decrease its count.
     * We usually use this to cache information about a user. Lets take for example
     * a user has 10 friends and instead of running a query to the database to check
     * how many friends they have we just store a static count in the database. So when
     * they add or remove a friend we then either increase or decrease the static record.
     *
     * Example:
     * <code>
     * Phpfox_Database::instance()->updateCounter('user_count', 'total_friend', 'user_id', 1);
     * </code>
     *
     * @param string $sTable Table to update
     * @param string $sCounter Field we are going to be updating. This is where the static value is
     * @param string $sField Field we need to identify the record we are going to be updating
     * @param int $iId ID of the field we are going to be updating
     * @param bool $bMinus False by default as we usually increase a count, if we decrease a count set this to true
     */
    public function updateCounter($sTable, $sCounter, $sField, $iId, $bMinus = false)
    {
        $iCount = $this->select($sCounter)->from(Phpfox::getT($sTable))->where($sField . ' = ' . (int) $iId)->execute('getSlaveField');

        $this->update(Phpfox::getT($sTable), array($sCounter => ($bMinus === true ? (($iCount <= 0 ? 0 : $iCount - 1)) : ($iCount + 1))), $sField . ' = ' . (int) $iId);
    }

    /**
     * This in practice works similar to our previous method self::updateCounter(), however
     * instead of increasing or decreasing a field it checks the table to see how many
     * rows there are and updates the static field with that count. This is usually only used
     * in the AdminCP to fix broken counters.
     *
     * @param string $sCountTable Table to check how many rows there are
     * @param array|string $aCountCond SQL conditional statement for the table we are checking
     * @param string $sCounter Field name of the table we are updating the static count
     * @param string $sUpdateTable Table we are going to be updating with the new count number
     * @param array|string $aUpdateCond SQL conditional statment for the table we are updating
     */
    public function updateCount($sCountTable, $aCountCond, $sCounter, $sUpdateTable, $aUpdateCond)
    {
        $iCount = $this->select('COUNT(*)')
            ->from(Phpfox::getT($sCountTable))
            ->where($aCountCond)
            ->execute('getSlaveField');

        $this->update(Phpfox::getT($sUpdateTable), array($sCounter => $iCount), $aUpdateCond);
    }

    /**
     * Gets all the joins made for the query.
     *
     * @return string Returns SQL joins
     */
    public function getJoins()
    {
        return $this->_aQuery['join'];
    }

    /**
     * Build search params for keywords.
     *
     * @param string $sField Field to search
     * @param string $sStr Keywords to use
     * @return string Returns an SQL ready search statement
     */
    public function searchKeywords($sField, $sStr)
    {
        if (is_array($sField))
        {
            $sQuery = '';
            $iIteration = 0;
            foreach ($sField as $sNewField)
            {
                $iIteration++;
                if ($iIteration != 1)
                {
                    $sQuery .= ' OR ';
                }
                $sQuery .= $this->searchKeywords($sNewField, $sStr);
            }

            return $sQuery;
        }

        $aWords = explode(' ', $sStr);

        $sQuery = ' (';
        if (count($aWords))
        {
            $iIteration = 0;
            foreach ($aWords as $sWord)
            {
                $sWord = trim($sWord);
                if (strlen($sWord) < 4)
                {
                    continue;
                }
                $iIteration++;
                if ($iIteration != 1)
                {
                    $sQuery .= ' OR ';
                }

                $sQuery .= $sField . ' LIKE \'%' . Phpfox_Database::instance()->escape($sWord) . '%\' ';

                $aLikeWords = $this->getLikeWords($sWord);

                foreach ($aLikeWords as $sLikeWord)
                {
                    if (strpos($sQuery, $sLikeWord) === false)
                    {
                        $sQuery .= ' OR ' . $sField . ' LIKE \'%' . Phpfox_Database::instance()->escape($sLikeWord) . '%\'' ;
                    }
                }

                $sQuery = rtrim($sQuery, ' OR ');
            }
        }

        if (!$iIteration)
        {
            return $sField . ' LIKE \'%' . Phpfox_Database::instance()->escape($sStr) . '%\' ';
        }

        $sQuery .= ') ';

        return $sQuery;
    }

    /**
     * Return data for you can easier process
     * @param $data: data return from query
     * @param $field_name: field you want to use
     * @return array
     */
    private function _singleData($data)
    {
        if (isset($this->_sSingleData))
        {
            $field_name = $this->_sSingleData;
            $this->singleData('');
            if (count($data))
            {
                $result = array();
                foreach ($data as $sub_data)
                {
                    $result[] = isset($sub_data[$field_name]) ? $sub_data[$field_name] : null;
                }
                return $result;
            } else
            {
                return $data;
            }
        } else
        {
            return $data;
        }

    }

    /**
     *	Takes into account html entities to return the ucwords and strtolower in an array.
     *	Mysql treats LIKE "%Something%" as => column LIKE "%Something" OR column LIKE "%something" but this doesnt work with non-english characters
     *	@return array
     *	@param $sWord string
     */
    private function getLikeWords($sWord)
    {
        if (preg_match('/(&#[0-9]+;)(.*)/', $sWord, $aMatch) < 1)
        {
            return array();
        }
        else if (isset($aMatch[2]))
        {
            $sFirstChar = $aMatch[1];
            $sFirstCharInChar = mb_decode_numericentity($sFirstChar, array(0x0, 0xffff, 0, 0x2ffff), 'UTF-8');
            $sRest = $aMatch[2];

            // Check its an html entity
            return array(
                (mb_encode_numericentity (mb_strtoupper($sFirstCharInChar, 'UTF-8'), array (0x0, 0xffff, 0, 0xffff), 'UTF-8')) . $sRest,
                (mb_encode_numericentity (mb_strtolower($sFirstCharInChar, 'UTF-8'), array (0x0, 0xffff, 0, 0xffff), 'UTF-8')) . $sRest,
            );
        }
    }

    protected function _where($sKey, $mValue) {
        if (is_array($mValue)) {
            $sWhere = 'AND ' . $sKey . '';
            $sKey = array_keys($mValue)[0];
            $sValue = array_values($mValue)[0];
            $sKey = strtolower($sKey);
            switch ($sKey) {
                case '=':
                    $sWhere .= ' = ' . $sValue . ' ';
                    break;
                case 'in':
                    $sWhere .= ' IN(' . $mValue[$sKey] . ')';
                    break;
                case 'like':
                    $sWhere .= ' LIKE \'' . $sValue . '\' ';
                    break;
            }

            return $sWhere;

        }
        $sWhere = 'AND ' . $sKey . ' = \'' . Phpfox_Database::instance()->escape($mValue) . '\' ';

        return $sWhere;
    }

    /**
     * Performs all the joins based on information passed from JOIN methods within this class.
     *
     * @see self::join()
     * @see self::leftJoin()
     * @see self::innerJoin()
     * @param string $sType The type of join we are going to use (LEFT JOIN, JOIN, INNER JOIM)
     * @param string $sTable Table to join
     * @param string $sAlias Alias to use to identify the table and make it unique
     * @param mixed $mParam Can be a string or an array of how to link the tables. This is usually a string that contains the part found with an SQL ON(__STRING__)
     */
    protected function _join($sType, $sTable, $sAlias, $mParam = null)
    {
        if (PHPFOX_DEBUG && in_array(strtoupper($sAlias), $this->_aWords))
        {
            Phpfox_Error::trigger('The alias "' . $sAlias . '" is a reserved SQL word. Use another alias to resolve this problem.', E_USER_ERROR);
        }

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
            if (preg_match("/(AND|OR|=|LIKE)/", $mParam))
            {
                $this->_aQuery['join'] .= "\n\tON({$mParam}";
            }
            else
            {
                // Not supported with other drivers so we don't use this anymore
                Phpfox_Error::trigger('Not allowed to use "USING()" in SQL queries any longer.', E_USER_ERROR);
            }
        }

        $this->_aQuery['join'] = preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->_aQuery['join'])) . ")\n";
    }

    /**
     * Insert data into the database
     *
     * @param string $sTable Database table
     * @param string $sFields List of fields
     * @param string $sValues List of values
     * @return string Returns the actual SQL query to perform
     */
    protected function _insert($sTable, $sFields, $sValues)
    {
        return 'INSERT INTO ' . $sTable . ' '.
            '        (' . $sFields . ')'.
            ' VALUES (' . $sValues . ')';
    }

    /**
     * Updates data in a specific table
     *
     * @param string $sTable Table we are updating
     * @param string $sSets SQL SET command
     * @param string $sCond SQL WHERE command
     * @return string Returns the actual SQL query to perform
     */
    protected function _update($sTable, $sSets, $sCond)
    {
        return 'UPDATE ' . $sTable . ' SET ' . $sSets . ' WHERE ' . $sCond;
    }

    /**
     * Returns one field from a row
     *
     * @param string $sSql SQL query
     * @param resource $hLink SQL resource
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

    public function createTable($sName, $aFields, $bCheckExists = false, $aKeys = [])
    {
        $sSql = 'CREATE TABLE ' . ($bCheckExists ? 'IF NOT EXISTS ' : '') . $sName . "\n";
        $sSql .= '(' ."\n";
        $bHasPK = false;
        $aPKeyNames = [];
        foreach ($aFields as $aField) {
            $aField['type'] = Phpfox::getLib('database.export')->getType('mysql', $aField['type']);
            $sSql .= $aField['name'] .' ' . $aField['type'] . (isset($aField['extra']) ? ' ' .$aField['extra'] : '' ) . ((isset($aField['auto_increment']) && $aField['auto_increment']) ? ' AUTO_INCREMENT' : '' ) . ",\n";
            if (isset($aField['primary_key']) && $aField['primary_key'] == true) {
                $aPKeyNames[] = $aField['name'];
                $bHasPK =  true;
            }
        }

        if ($bHasPK === true) {
            $sSql .= 'PRIMARY KEY (' . implode(', ', array_unique($aPKeyNames)) . ')' . ",\n";
        }

        // add keys
        foreach ($aKeys as $key_name => $key_fields) {
            if (!count($key_fields)) {
                continue;
            }
            $sSql .= "KEY `$key_name`(`". implode('`,`', $key_fields) ."`),\n";
        }

        $sSql = rtrim($sSql, ",\n") . ')';

        $this->query($sSql);
    }

    /**
     * Tells which index to use by issuing a Force Index ($sName)
     *
     * @param type String
     *
     * @return $this
     */
    public function forceIndex($sName)
    {
        if(!$sName)
            return $this;

        if (preg_match('/([a-zA-Z0-9_]+)/', $sName, $aMatches) > 0)
        {
            $this->_aQuery['forceIndex'] = $aMatches[1];
        }
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function forCount()
    {
        $this->_countState =  self::SELECT_FOR_COUNT;
        return $this;
    }

    /**
     * Rename table
     * @param $sOldTableName
     * @param $sNewTableName
     */
    public function renameTable($sOldTableName, $sNewTableName)
    {
        if (!$this->tableExists($sOldTableName)) {
            return;
        }

        $this->query("RENAME TABLE `$sOldTableName` TO `$sNewTableName`;");
    }

    public function ping()
    {
        return mysql_ping($this->_hMaster);
    }

    public function reconnect($force_reconnect = false)
    {
        if($force_reconnect OR !$this->ping()){ // avoid re-connect if server is alive.
            $this->_hMaster = null;
            $this->_hSlave = null;
            return $this->connect(Phpfox::getParam(array('db', 'host')), Phpfox::getParam(array('db', 'user')), Phpfox::getParam(array('db', 'pass')), Phpfox::getParam(array('db', 'name')), Phpfox::getParam(array('db', 'port')));
        }
        return true;
    }
}
