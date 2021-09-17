<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 */
class Language_Service_Callback extends Phpfox_Service
{
    /**
     * Language_Service_Callback constructor.
     */
	public function  __construct()
	{
		$this->_sTable = Phpfox::getT('language');
	}

    public function exportModule($sProduct, $sModule, $bCore)
    {
        return Phpfox::getService('language')->exportForModule($sModule);
    }
}