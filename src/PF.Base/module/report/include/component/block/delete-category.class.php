<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Report_Component_Block_Delete_Category extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
        $iReportId = $this->request()->getInt('report_id');
        $this->template()->assign(array(
                'iReportId' => $iReportId,
                'aAnotherCategories' => Phpfox::getService('report')->getAnotherCategories($iReportId),
                'iNumberOfReport' => Phpfox::getService('report.data')->getReportsCountByCategoryId($iReportId)
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('report.component_block_delete_category_clean')) ? eval($sPlugin) : false);
    }
}