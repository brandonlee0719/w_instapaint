<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Phpfox_Component
 * @version        $Id: add.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Report_Component_Controller_Admincp_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('id'))) {
            if ($aCategory = Phpfox::getService('report')->getForEdit($iId)) {
                $bIsEdit = true;
                $this->template()->assign(['aForms'=> $aCategory,'sPhraseVarName'=>$aCategory['message']]);
            }
        }
        $aLanguages = Phpfox::getService('language')->getAll(true);
        if (($aVals = $this->request()->getArray('val'))) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit && isset($aCategory)) {
                    if (Phpfox::getService('report.process')->update($aCategory['report_id'], $aVals)) {
                        $this->url()->send('admincp.report.add', array('id' => $aCategory['report_id']), _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('report.process')->add($aVals)) {
                        $this->url()->send('admincp.report.add', null, _p('category_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit === true ? _p('edit_a_category') : _p('add_a_category')))
            ->setBreadCrumb(($bIsEdit === true ? _p('edit_a_category') : _p('add_a_category')), $this->url()->makeUrl('admincp.report'))
            ->setActiveMenu('admincp.maintain.report')
            ->assign(array(
//                    'aLanguages' => $aLanguages,
                    'bIsEdit' => $bIsEdit
                )
            );
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('report.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}