<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: stat.html.php 4095 2012-04-16 13:29:01Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !empty($sStartTime)}
Viewing stats from <strong>{$sStartTime}</strong> until <strong>{$sEndTime}</strong>.
{/if}
<div class="table-responsive">
    <table class="table table-admin" id="js_core_site_stat">
        <thead>
            <tr>
                <th>{_p var='name'}</th>
                <th>{_p var='total'}</th>
                <th>{_p var='daily_average'}</th>
            </tr>
        </thead>
        <tbody>
            {if empty($aStats)}
            <tr id="js_core_site_stat_build">
                <td colspan="3">
                    {_p var='building_site_stats_please_hold'}
                    <script type="text/javascript">
                        {literal}
                        $Behavior.buildCoreSiteStats = function(){
                            $.ajaxCall('core.buildStats', '', 'GET');
                        }
                        {/literal}
                    </script>
                </td>
            </tr>
            {else}
            {foreach from=$aStats name=stats item=aStat}
            {template file='core.block.admin-stattr'}
            {/foreach}
            {/if}
        </tbody>
    </table>
</div>