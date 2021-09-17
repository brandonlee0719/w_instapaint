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
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Language_Component_Controller_Admincp_Missing extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        Core\Lib::phrase()->findMissingPhrases($this->request()->get('id'));

        Phpfox::getLib('template')
            ->setBreadCrumb(_p('find_missing_phrases'));
        
        $sPhrase = _p('successfully_imported_missing_phrases');

        Phpfox::getLib('cache')->removeGroup('locale');
        
        if ($this->request()->get('check') == 'true') {
            $this->url()->send('admincp.language', null, $sPhrase);
        } else {
            $this->url()->send('admincp.language.missing', ['id' => $this->request()->get('id'), 'check' => 'true']);
        }
    }
    
    /**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_missing_clean')) ? eval($sPlugin) : false);
	}
}