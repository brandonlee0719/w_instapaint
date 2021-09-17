<?php

defined('PHPFOX') or exit('NO DICE!');


class Admincp_Service_Alert extends Phpfox_Service
{
    /**
     * @return int
     */
    public function getAdminMenuBadgeNumber()
    {
        return count($this->getItems());
    }

    /**
     * @return array
     */
    public function getItems()
    {

        /**
         * Item format
         * $aItems[] = [
         *      'link'=> '',
         *      'message'=> '',
         * ]
         */
        $aItems = [];

        (($sPlugin = Phpfox_Plugin::get('admincp_alert_item')) ? eval($sPlugin) : false);

        // get pending

        $pendingApprovals = Phpfox::massCallback('getAdmincpAlertItems');

        if (is_array($pendingApprovals)) {
            foreach ($pendingApprovals as $aValue) {
                if(isset($aValue[0]) && is_array($aValue[0])){
                    foreach($aValue as $aSubValue){
                        if(isset($aSubValue['value']) and $aSubValue['value']>0){
                            $aItems[] =  $aSubValue;
                        }
                    }
                }else if(isset($aValue['value']) and $aValue['value']>0){
                    $aItems [] = $aValue;
                }
            }
        }
        return $aItems;
    }
}