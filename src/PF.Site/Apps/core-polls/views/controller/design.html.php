<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($bDesign) && $bDesign}
	<form action="{url link='poll.design' id=$aPoll.poll_id}" method="post" id="js_poll_design_form">
		<div><input type="hidden" name="val[poll_id]" id="iPoll" value="{$aPoll.poll_id}" /></div>

		<ul id="js_poll_design_wrapper" class="dont-unbind-children poll-app clearfix">
			<li class="design-control">
				<b class="mr-1">{_p var='background'}</b>
				<input type="hidden" name="val[js_poll_background]" value="{if $aPoll.background}{$aPoll.background}{else}ebebeb{/if}" data-rel="backgroundChooser" class="_colorpicker" />
				<div class="_colorpicker_holder"></div>
			</li>
			<li class="design-control">
				<b class="mr-1">{_p var='percent'}</b>
				<input type="hidden" name="val[js_poll_percentage]" value="{if $aPoll.percentage}{$aPoll.percentage}{else}297fc7{/if}" data-rel="percentageChooser" class="_colorpicker" />
				<div class="_colorpicker_holder"></div>
			</li>
			<li class="design-control">
				<b class="mr-1">{_p var='border'}</b>
				<input type="hidden" name="val[js_poll_border]" value="{$aPoll.border}" data-rel="borderChooser" class="_colorpicker" />
				<div class="_colorpicker_holder"></div>
			</li>
		</ul>
	</form>
{/if}

{template file='poll.block.entry'}

{if isset($bDesign) && $bDesign}
    <ul class="table_clear_button">
        <li><input type="button" value="{_p var='save'}" class="button btn-primary" onclick="$('#js_poll_design_form').submit();"/></li>
        <li><a href="javascript:void(0)" onclick="window.location.href='{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}';">{_p var="Cancel"}</a></li>
    </ul>
{/if}