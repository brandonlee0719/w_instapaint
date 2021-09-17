<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: system.class.php 5456 2013-02-28 14:24:45Z Miguel_Espinoza $
 */
class Core_Service_System extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct() {}
    
    /**
     * @return array
     */
	public function get()
	{
		$oFile = Phpfox_File::instance();
		$bSlaveEnabled = Phpfox::getParam(array('db', 'slave'));
		$sDriver = Phpfox::getParam(array('db', 'driver'));

		$aStats = array
	    (
	    	_p('phpfox_version') => Phpfox::getVersion(),
			_p('php_version') => '<a href="' . Phpfox_Url::instance()->makeUrl('admincp.core.phpinfo') . '">' . PHP_VERSION . '</a>',
	    	_p('php_sapi') => php_sapi_name(),
	    	_p('php_safe_mode') => (PHPFOX_SAFE_MODE ? _p('true') : _p('false')),
	    	_p('php_open_basedir') => (PHPFOX_OPEN_BASE_DIR ? _p('true') : _p('false')),
	    	_p('php_disabled_functions') =>  (@ini_get('disable_functions') ? str_replace( ",", ", ", @ini_get('disable_functions') ) : _p('none')),
	    	_p('php_loaded_extensions') => implode(' ', get_loaded_extensions()),
	    	_p('operating_system') => PHP_OS,
	    	_p('server_time_stamp') => date('F j, Y, g:i a', PHPFOX_TIME) . ' (GMT)',
	    	_p('gzip') => (Phpfox::getParam('core.use_gzip') ? _p('enabled') : _p('disabled')),
	    	_p('sql_driver_version') =>  ($sDriver == 'DATABASE_DRIVER' ? _p('n_a') : Phpfox_Database::instance()->getServerInfo()),
	    	_p('sql_slave_enabled') => ($bSlaveEnabled ? _p('yes') : _p('no')),
	    	_p('sql_total_slaves') => ($bSlaveEnabled ? count(Phpfox::getParam(array('db', 'slave_servers'))) : _p('n_a')),
	    	_p('sql_slave_server') => ($bSlaveEnabled ? Phpfox_Database::instance()->sSlaveServer : _p('n_a')),
	    	_p('memory_limit') => $oFile->filesize($this->_getUsableMemory()) . ' (' . @ini_get('memory_limit') . ')',
	    	_p('load_balancing_enabled') => (Phpfox::getParam(array('balancer', 'enabled')) ? _p('yes') : _p('no'))
	    );
	    
	    if(strpos(strtolower(PHP_OS), 'win') === 0 || PHPFOX_SAFE_MODE || PHPFOX_OPEN_BASE_DIR)
		{
			
		}
		else 
		{
			if (function_exists('shell_exec'))
			{
				$sMemory = @shell_exec("free -m");
				$aMemory = explode("\n", str_replace( "\r", "", $sMemory));
				if (is_array($aMemory))
				{
					$aMemory = array_slice($aMemory, 1, 1);
					if (isset($aMemory[0]))
					{
						$aMemory = preg_split("#\s+#", $aMemory[0]);

						$aStats[_p('total_server_memory')]	= (isset($aMemory[1]) ? $aMemory[1] . ' MB' : '--');
						$aStats[_p('available_server_memory')]	= (isset($aMemory[3]) ? $aMemory[3] . ' MB' : '--');
					}
				}
			}
            else if (stristr(PHP_OS, "win") === false)
            {
                $sMemory = file_get_contents('/proc/meminfo'); 
                $aMemoryStats = explode("n", $sMemory); // escape the "new line" 
                $aMem = null; 
                foreach($aMemoryStats as $iKey => $sMemoryStat) 
                { 
                    $aMemoryStats[$iKey] = preg_replace('/s+/', ' ', $sMemoryStat); 
                    if(preg_match('/[0-9]+/', $sMemoryStat, $aMem)) 
                    { 
                        $aMemoryStats[$iKey] = ($aMem[0]/1024); 
                    } 
                } 
                $aStats[_p('total_server_memory')] = (int)$aMemoryStats[0] . ' MB';
                $aStats[_p('available_server_memory')] = (int)$aMemoryStats[1] . ' MB';
            }
		}
		
		if (!PHPFOX_OPEN_BASE_DIR && ($sLoad = Phpfox::getService('core.load')->get()) !== null)
		{
			$aStats[_p('current_server_load')] = $sLoad;
		}
	    
	    return $aStats;
	}
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
        if ($sPlugin = Phpfox_Plugin::get('core.service_system__call')) {
            eval($sPlugin);
            return null;
        }
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
    
    /**
     * @return int
     */
	private static function _getUsableMemory()
	{
        $sVal = trim(@ini_get('memory_limit'));
        
        if (preg_match('/(\\d+)([mkg]?)/i', $sVal, $aRegs)) {
            $sMemoryLimit = (int)$aRegs[1];
            switch ($aRegs[2]) {
                case 'k':
                case 'K':
                    $sMemoryLimit *= 1024;
                    break;
                case 'm':
                case 'M':
                    $sMemoryLimit *= 1048576;
                    break;
                case 'g':
                case 'G':
                    $sMemoryLimit *= 1073741824;
                    break;
            }
            
            $sMemoryLimit /= 2;
        } else {
            $sMemoryLimit = 1048576;
        }
        
        return $sMemoryLimit;
	}		
}