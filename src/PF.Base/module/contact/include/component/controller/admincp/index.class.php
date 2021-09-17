<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.class.php 6113 2013-06-21 13:58:40Z Raymond_Benc $
 */
class Contact_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        $this->template()->setTitle(_p('categories'))
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('contact'), $this->url()->makeUrl('admincp.contact'))
            ->setBreadCrumb(_p('categories'))
            ->assign(array(
                    'aCategories' => Phpfox::getService('contact')->getCategories(),
                    'aLanguages'  => Phpfox::getService('language')->getAll()
                )
            )
            ->setHeader('cache', array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'contact.manageOrdering\'}); }</script>'
                )
            );
        // populate edit
        if ($iEditId = $this->request()->get('edit')) {
            $aCategory = Phpfox::getService('contact')->getCategoryById($iEditId);
            $this->template()->assign('aForms', $aCategory);
        }

		// is it adding a new category
		if ("add" == $this->request()->get('action'))
		{
            $aVals = $this->request()->getArray('val');
            if ($aVals = $this->_validate($aVals)) {
                if (Phpfox::getService('contact.process')->addCategory($aVals))
                {
                    $this->url()->send('admincp.contact',null,_p('category_succesfully_added'));
                }
                else
                {
                    $this->url()->send('admincp.contact',null,_p('category_could_not_be_added'));
                }
            }
		} elseif ("edit" == $this->request()->get('action')) {
            $iEdit = $this->request()->getInt('iEdit');
            $aVals = $this->request()->getArray('val');
            if ($aVals = $this->_validate($aVals)) {
                if (Phpfox::getService('contact.process')->updateCategory($aVals, $iEdit)) {
                    $this->url()->send('admincp.contact',null,_p('category_succesfully_updated'));
                }
            }
        }
		// is it deleting categories
		if ($this->request()->get('delete') && $aDeleteIds = $this->request()->getArray('id'))
		{
			if (Phpfox::getService('contact.process')->deleteMultiple($aDeleteIds))
			{
				$this->url()->send('admincp.contact', null, _p('categories_successfully_deleted'));
			}
		}
	}

    /**
     * validate input value
     * @param $aVals
     *
     * @return mixed
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	(($sPlugin = Phpfox_Plugin::get('contact.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}