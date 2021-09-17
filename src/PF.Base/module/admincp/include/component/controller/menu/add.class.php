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
 * @package  		Module_Admincp
 * @version 		$Id: add.class.php 4565 2012-07-26 12:02:50Z Miguel_Espinoza $
 */
class Admincp_Component_Controller_Menu_Add extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		$oAdmincpMenu =  Phpfox::getService('admincp.menu');
		$bIsEdit = false;
		$bIsPage = false;
		
		if (Phpfox::isModule('page') && ($sPage = $this->request()->get('page')))
		{
			$aPage = Phpfox::getService('page')->getPage($sPage, true);
			if (isset($aPage['page_id']))
			{
				$bIsPage = true;	
				$this->template()->assign(array(
						'aPage' => $aPage,
						'sModuleValue' => ($aPage['module_id'] ? $aPage['module_id'] . '|' . $aPage['module_id'] : 'page|page'),
						'aAccess' => (empty($aPage['disallow_access']) ? null : unserialize($aPage['disallow_access']))
					)
				);
			}
		}

        if (($iEditId = $this->request()->getInt('id')) || ($iEditId = $this->request()->getInt('menu_id'))) {
            $aRow = $oAdmincpMenu->getForEdit($iEditId);
            $aLanguages = Phpfox::getService('language')->getWithPhrase($aRow['var_name']);
            $bIsEdit = true;
            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'aAccess' => (empty($aRow['disallow_access']) ? null : unserialize($aRow['disallow_access']))
                )
            );
        } else {
            $aLanguages = Phpfox::getService('language')->get();
        }

        if ($aVals = $this->request()->getArray('val')) {
		    if (empty($aVals['m_connection'])) {
                Phpfox_Error::set(_p('placement_is_required'));
            } elseif (Phpfox::getService('language')->validateInput($aVals, 'text')) {
                if ($bIsEdit) {
                    $sMessage = _p('menu_successfully_updated');
                    Phpfox::getService('admincp.menu.process')->update($aRow['menu_id'], $aVals);
                } else {
                    $sMessage = _p('menu_successfully_added');
                    Phpfox::getService('admincp.menu.process')->add($aVals);
                }

                if (isset($aVals['is_page'])) {
                    $this->url()->send($aVals['url_value'], null, _p('page_menu_successfully_added'));
                }

                if ($bIsEdit) {
                    $this->url()->send('admincp.menu', null, $sMessage);
                } else {
                    $this->url()->send('admincp.menu', null, $sMessage);
                }
            }
        }
		
		$this->template()->assign(array(
				'aProducts' => Phpfox::getService('admincp.product')->get(),
				'aModules' => Phpfox::getService('admincp.module')->getModules(),
				'aParents' => Phpfox::getService('admincp.menu')->get(array('menu.parent_id = 0 AND menu.m_connection IN(\'main\', \'main_right\')'), false),
				'aControllers' => Phpfox::getService('admincp.component')->get(true),
				'aPages' => Phpfox::getService('page')->getCache(),
				'aLanguages' => $aLanguages,
				'aUserGroups' => Phpfox::getService('user.group')->get(),
				'aTypes' => $oAdmincpMenu->getTypes(),
				'bIsEdit' => $bIsEdit,
				'bIsPage' => $bIsPage
			)
		)
            ->setActiveMenu('admincp.appearance.menu')
		->setBreadCrumb(_p('add_new_menu'), $this->url()->makeUrl('current'), true)
		->setTitle(_p('add_new_menu'));
	}
}