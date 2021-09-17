<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Core_Component_Block_Fe_Site_Stat extends Phpfox_Component
{
	/**
	 * Controller
     * Todo should implement cache but in 05 minutes.
	 */
	public function process()
	{
        $aOnline = Phpfox::getService('log.session')->getOnlineStats();
        $bShowTodayStats = $this->getParam('show_today', true);
        $bShowAllTimeStats = $this->getParam('show_all_time', true);
        $bShowOnlineMember = $this->getParam('show_online', true);
        $iTotalOnlineMember = 0;
        $aTodayStats = [];
        $aAllTimeStats = [];

        if ($bShowOnlineMember) {
            $iTotalOnlineMember = $aOnline['members']+ $aOnline['guests'];
        }

        if ($bShowTodayStats) {
            $aStats = Phpfox::massCallback('getSiteStatsForAdmins');
            foreach ($aStats as $aStat) {
                if (!$aStat['value']) {
                    continue;
                }
                $aTodayStats[] = $aStat;
            }
            if(empty($aStats)){
                $bShowTodayStats = false;
            }
        }


        if ($bShowAllTimeStats) {
            // All time statistics
            $aStats = Phpfox::getService('core.stat')->getSiteStatsForAdmin(0, 0);
            foreach($aStats as $aStat){
                if(!$aStat['total']){
                    continue;
                }
                $aAllTimeStats[] = [
                    'phrase' => isset($aStat['phrase']) ? _p($aStat['phrase']) : '',
                    'value' => $aStat['total']
                ];
            }

            if(empty($aAllTimeStats)){
                $bShowAllTimeStats =  0;
            }
        }

        $this->template()->assign(array(
                'bShowOnlineMember'=>$bShowOnlineMember,
                'bShowTodayStats'=>$bShowTodayStats,
                'bShowAllTimeStats'=>$bShowAllTimeStats,
                'iTotalOnlineMember'=>$iTotalOnlineMember,
                'aTodayStats'=>$aTodayStats,
                'aAllTimeStats'=>$aAllTimeStats,
                'sHeader' => _p('site_statistics'),
            )
        );

        return 'block';
	}

	public function getSettings()
    {
        return [
            [
                'info' => _p('show_number_of_online_members'),
                'value' => true,
                'var_name' => 'show_online',
                'type' => 'boolean'
            ],
            [
                'info' => _p('show_today_stats'),
                'value' => true,
                'var_name' => 'show_today',
                'type' => 'boolean'
            ],
            [
                'info' => _p('show_all_time_stats'),
                'value' => true,
                'var_name' => 'show_all_time',
                'type' => 'boolean'
            ]
        ];
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_fe_site_stat_clean')) ? eval($sPlugin) : false);
	}
}
