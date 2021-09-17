<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: moderation.html.php 4086 2012-04-05 12:32:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='current'}" id="js_global_multi_form_holder" class="form">
    {if !empty($sCustomModerationFields)}
	{$sCustomModerationFields}
	{/if}
	<div id="js_global_multi_form_ids">{$sInputFields}</div>
    <div class="moderation_placeholder">
        <span class="moderation_process">{img theme='ajax/add.gif'}</span>
        <button class="btn btn-sm btn-primary" data-cmd="core.toggle_check_all" type="button" data-txt1="{_p var='select_all'}" data-txt2="{_p var='un_select_all'}">{_p var='select_all'}</button>
        <div class="btn-group btn-group-sm dropup moderation-dropdown hide" id="moderation_drop_down">
            <button type="button" class="btn btn-xs btn-primary" data-toggle="dropdown">
                {_p var='with_selected'} <span id="moderation_badge">8</span> <i class="fa fa-caret-up"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" id="moderation_menu">
                {foreach from=$aModerationParams.menu item=aModerationMenu}
                <li>
                    <a data-cmd="core.moderation_action" href="#{$aModerationMenu.action}" class="moderation_process_action" rel="{$aModerationParams.ajax}" {if !empty($aModerationParams.extra)}data-extra="{$aModerationParams.extra}"{/if}>{$aModerationMenu.phrase}</a>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
	<div class="moderation_holder hide btn-group dropup {if !$iTotalInputFields} not_active{/if}">
		<a role="button" class="btn btn-sm moderation_drop pull-left"><span> (<strong class="js_global_multi_total">{$iTotalInputFields}</strong>)</span></a>
		<a role="button" class="moderation_action moderation_action_select btn btn-sm pull-right"
		   rel="select">{_p var='select_all'}
		</a>
	</div>
</form>

<script>
    var moderationViewString = '{$sView}';
</script>