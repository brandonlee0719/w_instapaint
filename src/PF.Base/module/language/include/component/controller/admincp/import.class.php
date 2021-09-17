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
 * @version 		$Id: import.class.php 4961 2012-10-29 07:11:34Z Raymond_Benc $
 */
class Language_Component_Controller_Admincp_Import extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$iPage = $this->request()->getInt('page', 0);
		$bImportPhrases = false;
		$base = true;

		if ($install = $this->request()->get('install')) {
			$base = false;
			$dir = PHPFOX_DIR_INCLUDE . 'xml/language/' . $install . '/';
			Phpfox::getService('language.process')->installPackFromFolder($install, $dir);

			$this->request()->set('dir', $dir);
			if (!is_dir($dir)) {
                Phpfox_Error::set(_p('language_package_cannot_be_found_at_dir', ['dir' => $dir]));
			}
		}

		if (($dir = $this->request()->get('dir'))) {
			$dir = ($base ? base64_decode($dir) : $dir);
			$parts = explode('language/', rtrim($dir, '/'));

			$bImportPhrases = true;
			$mReturn = Phpfox::getService('language.phrase.process')->installFromFolder($parts[1], $dir, $iPage);
			if ($mReturn === 'done')
			{
				$sPhrase = _p('successfully_installed_the_language_package');

                Phpfox::getLib('cache')->removeGroup('locale');

				$this->url()->send('admincp.language', null, $sPhrase);
			}
			else
			{
				if ($mReturn)
				{
					$this->template()->setHeader('<meta http-equiv="refresh" content="2;url=' . $this->url()->makeUrl('admincp.language.import', array('dir' => base64_encode($dir), 'page' => ($iPage + 1))) . '">');
				}
			}
		}

		$this->template()->setTitle(_p('manage_language_packages'))
			->setBreadCrumb(_p('manage_language_packages'))
            ->setActiveMenu('admincp.globalize.language')
			->assign(array(
					'aNewLanguages' => Phpfox::getService('language')->getForInstall(),
					'bImportPhrases' => $bImportPhrases
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_import_clean')) ? eval($sPlugin) : false);
	}
}