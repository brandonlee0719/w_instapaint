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
 * @version 		$Id: category.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Report_Component_Controller_Admincp_Category extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($aIds = $this->request()->getArray('id')))
        {
            foreach ($aIds as $iId)
            {
                if (!is_numeric($iId))
                {
                    continue;
                }

                Phpfox::getService('report.process')->delete($iId);
            }

            $this->url()->send('admincp.report.category', null, _p('successfully_deleted_categories'));
        }

        if ($iId = $this->request()->getInt('report_id')) {
            switch ($this->request()->get('child_action')) {
                case 'move':
                    Phpfox::getService('report.data.process')->moveReportToAnother($iId, $this->request()->get('category_id'));
                    break;
                default:
                    Phpfox::getService('report.data.process')->ignoreByReportId($iId);
                    break;
            }
            Phpfox::getService('report.process')->delete($iId);
        }

        $this->template()->setTitle(_p('manage_categories'))
            ->setBreadCrumb(_p('manage_categories'), $this->url()->makeUrl('admincp.report'))
            ->setPhrase(['delete_category'])
            ->setActiveMenu('admincp.maintain.report')
            ->assign(array(
                    'aCategories' => Phpfox::getService('report')->getCategories()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('report.component_controller_admincp_category_clean')) ? eval($sPlugin) : false);
    }
}