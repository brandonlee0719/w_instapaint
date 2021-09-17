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
 * @package  		Module_Report
 * @version 		$Id: process.class.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
class Report_Service_Data_Process extends Phpfox_Service 
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('report_data');
    }

    public function add($iReportId, $sType, $iItemId, $sFeedback = '')
    {
        if (empty($iReportId)){
            return Phpfox_Error::set(_p('provide_a_category_name'));
        }
        $this->database()->insert($this->_sTable, array(
                'report_id' => (int) $iReportId,
                'item_id' => $sType . '_' . (int) $iItemId,
                'user_id' => Phpfox::getUserId(),
                'added' => PHPFOX_TIME,
                'ip_address' => Phpfox_Request::instance()->getServer('REMOTE_ADDR'),
                'feedback' => $sFeedback != '' ? Phpfox::getLib('parse.input')->clean($sFeedback) : ''
            )
        );

        return true;
    }

    public function ignore($iId)
    {
        static $aCache = array();

        $aReport = $this->database()->select('data_id, item_id')
            ->from(Phpfox::getT('report_data'))
            ->where('data_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        if (!isset($aReport['data_id']))
        {
            return false;
        }

        if (!isset($aCache[$aReport['item_id']]))
        {
            $this->database()->delete(Phpfox::getT('report_data'), 'item_id = \'' . $aReport['item_id'] . '\'');

            $aCache[$aReport['item_id']] = true;
        }

        return true;
    }

    /**
     * @param $iId
     * @return bool
     */
    public function process($iId)
    {
        $aReport = $this->database()->select('data_id, item_id, user_id')
            ->from(Phpfox::getT('report_data'))
            ->where('data_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!isset($aReport['data_id'])) {
            return false;
        }

        if (isset($aReport['item_id'])) {
            //notify for reporter
            $link = Phpfox::getService('report')->getRedirect($aReport['data_id']);

            Phpfox::getLib('mail')->to($aReport['user_id'])
                ->subject(_p('Your report is processed'))
                ->message(_p('Your report for item {link} is processed', ['link' => $link]))
                ->send();

            $this->database()->delete(Phpfox::getT('report_data'), 'item_id = \'' . $aReport['item_id'] . '\'');
            $aReport['item_id'] = true;
        }

        return true;
    }

    /**
     * Ignore all report data by report id
     * @param $iReportId
     * @return bool
     */
    public function ignoreByReportId ($iReportId)
    {
        return db()->delete($this->_sTable, ['report_id' => (int) $iReportId]);
    }

    /**
     * Move all report from old category to new
     * @param $iOldReportId
     * @param $iNewReportId
     * @return bool|resource
     */
    public function moveReportToAnother ($iOldReportId, $iNewReportId)
    {
        return db()->update($this->_sTable, ['report_id' => (int) $iNewReportId], ['report_id' => (int) $iOldReportId]);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('report.service_data_process__call'))
        {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}