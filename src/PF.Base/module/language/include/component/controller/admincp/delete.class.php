<?php
defined('PHPFOX') or exit('NO DICE!');

class Language_Component_Controller_Admincp_Delete extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{	
		Phpfox::getUserParam('language.can_manage_lang_packs', true);

		$iId = $this->request()->get('id');

        if (!$iId) {
            return Phpfox_Error::display(_p('invalid_language'));
        }
		
		$aLanguage = Phpfox::getService('language')->getLanguage($iId);
		
		if (!isset($aLanguage['language_id']))
		{
			return Phpfox_Error::display(_p('invalid_language_package'));
		}

        if (Phpfox::getService('language.process')->delete($iId)) {
            $this->url()->send('admincp.language', _p('language_package_successfully_deleted'));
        }

		$this->template()->assign(array(
			'aLanguage' => $aLanguage
		))->setTitle(_p('manage_language_packages'))
			->setTitle(_p('delete'))
			->setBreadCrumb(_p('manage_language_packages'))
			->setBreadCrumb(_p('delete'));
        return null;
	}
}
